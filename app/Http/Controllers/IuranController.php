<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Iuran;
use App\Models\IuranPembayaran;
use App\Models\IuranPeserta;
use App\Models\TransaksiKeuangan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class IuranController extends Controller
{
    /** List semua tagihan iuran + progres terkumpul. */
    public function index(Request $request)
    {
        $query = Iuran::withCount('pesertas')
            ->withSum('pembayarans as terkumpul', 'jumlah')
            ->latest();

        if ($search = $request->input('search', '')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $iurans = $query->paginate(10)->withQueryString();

        // Hitung target per iuran (sum nominal_wajib) tanpa N+1 berat.
        $targetMap = IuranPeserta::whereIn('iuran_id', $iurans->pluck('id'))
            ->selectRaw('iuran_id, SUM(nominal_wajib) as total')
            ->groupBy('iuran_id')
            ->pluck('total', 'iuran_id');

        $iurans->getCollection()->transform(function ($i) use ($targetMap) {
            $target = (int) ($targetMap[$i->id] ?? 0);
            $terkumpul = (int) ($i->terkumpul ?? 0);
            $i->target = $target;
            $i->terkumpul = $terkumpul;
            $i->sisa = max(0, $target - $terkumpul);
            $i->progress = $target > 0 ? round($terkumpul / $target * 100) : 0;

            return $i;
        });

        return Inertia::render('Admin/Iuran/Index', [
            'iurans' => $iurans,
            'filters' => $request->only(['search', 'status']),
            'statusList' => Iuran::STATUS,
            'user' => Auth::user(),
        ]);
    }

    /** Form buat iuran baru + picker peserta (cari by nama/role/prodi). */
    public function create()
    {
        return Inertia::render('Admin/Iuran/Create', [
            'prodiList' => User::PRODI,
            'rolesList' => Role::orderBy('name')->pluck('name'),
            'user' => Auth::user(),
        ]);
    }

    /** Endpoint JSON untuk picker peserta: ?search=&prodi=&role=&limit=20 */
    public function cariUser(Request $request)
    {
        $query = User::query()->select(['id', 'name', 'nim', 'foto', 'prodi', 'role', 'status']);

        if ($search = $request->input('search', '')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }
        if ($request->filled('prodi')) {
            $query->where('prodi', $request->input('prodi'));
        }
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        // Sembunyikan yang sudah jadi peserta iuran ini (biar tidak dobel).
        if ($request->filled('except_iuran')) {
            $sudah = IuranPeserta::where('iuran_id', $request->input('except_iuran'))->pluck('user_id');
            $query->whereNotIn('id', $sudah);
        }

        return response()->json(
            $query->orderBy('name')->limit(min(50, (int) $request->input('limit', 20)))->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'deskripsi' => 'nullable|string|max:1000',
            'nominal' => 'required|integer|min:1000|max:999999999999',
            'jatuh_tempo' => 'nullable|date',
            'peserta_ids' => 'required|array|min:1|max:2000',
            'peserta_ids.*' => 'exists:users,id',
        ]);

        $iuran = DB::transaction(function () use ($validated) {
            $iuran = Iuran::create([
                'nama' => $validated['nama'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'nominal' => $validated['nominal'],
                'jatuh_tempo' => $validated['jatuh_tempo'] ?? null,
                'status' => Iuran::STATUS_AKTIF,
                'created_by' => Auth::id(),
            ]);

            foreach (array_unique($validated['peserta_ids']) as $uid) {
                IuranPeserta::firstOrCreate(
                    ['iuran_id' => $iuran->id, 'user_id' => $uid],
                    ['nominal_wajib' => $validated['nominal']]
                );
            }

            return $iuran;
        });

        ActivityLog::record(Auth::user(), ActivityLog::CREATE, "Membuat iuran {$iuran->nama} Rp ".number_format($iuran->nominal, 0, ',', '.').' untuk '.count($validated['peserta_ids']).' peserta.');

        return redirect()->route('iuran.show', $iuran)->with('success', 'Iuran berhasil dibuat.');
    }

    /** Detail iuran: siapa sudah/belum + sisa per orang (mendukung cicilan). */
    public function show(Request $request, Iuran $iuran)
    {
        $pesertaQuery = $iuran->pesertas()->with(['user:id,name,nim,foto,prodi,role', 'pembayarans' => fn ($q) => $q->latest()]);

        if ($search = $request->input('search', '')) {
            $pesertaQuery->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('nim', 'like', "%{$search}%"));
        }
        if ($request->filled('prodi')) {
            $pesertaQuery->whereHas('user', fn ($q) => $q->where('prodi', $request->input('prodi')));
        }

        $semua = $pesertaQuery->get();

        // Hitung bayar per peserta + filter status di memori (sum relasi).
        $rows = $semua->map(function (IuranPeserta $p) {
            $bayar = (int) $p->pembayarans->sum('jumlah');
            $sisa = max(0, (int) $p->nominal_wajib - $bayar);
            $status = $bayar <= 0 ? 'belum' : ($bayar < (int) $p->nominal_wajib ? 'sebagian' : 'lunas');

            return [
                'id' => $p->id,
                'user_id' => $p->user_id,
                'nominal_wajib' => (int) $p->nominal_wajib,
                'total_bayar' => $bayar,
                'sisa' => $sisa,
                'status_bayar' => $status,
                'user' => $p->user,
                'pembayarans' => $p->pembayarans->map(fn ($b) => [
                    'id' => $b->id,
                    'jumlah' => (int) $b->jumlah,
                    'tanggal' => $b->tanggal,
                    'keterangan' => $b->keterangan,
                ]),
            ];
        });

        if ($request->filled('status_bayar')) {
            $rows = $rows->where('status_bayar', $request->input('status_bayar'))->values();
        }

        $target = (int) $semua->sum('nominal_wajib');
        $terkumpul = (int) $semua->sum(fn ($p) => (int) $p->pembayarans->sum('jumlah'));

        return Inertia::render('Admin/Iuran/Show', [
            'iuran' => $iuran,
            'pesertas' => $rows->values(),
            'filters' => $request->only(['search', 'prodi', 'status_bayar']),
            'prodiList' => User::PRODI,
            'ringkasan' => [
                'total_peserta' => $semua->count(),
                'lunas' => $rows->where('status_bayar', 'lunas')->count(),
                'sebagian' => $rows->where('status_bayar', 'sebagian')->count(),
                'belum' => $rows->where('status_bayar', 'belum')->count(),
                'target' => $target,
                'terkumpul' => $terkumpul,
                'sisa' => max(0, $target - $terkumpul),
                'progress' => $target > 0 ? round($terkumpul / $target * 100) : 0,
            ],
            'user' => Auth::user(),
        ]);
    }

    public function edit(Iuran $iuran)
    {
        return Inertia::render('Admin/Iuran/Edit', [
            'iuran' => $iuran,
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request, Iuran $iuran)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'deskripsi' => 'nullable|string|max:1000',
            'nominal' => 'required|integer|min:1000|max:999999999999',
            'jatuh_tempo' => 'nullable|date',
            'status' => ['required', Rule::in(Iuran::STATUS)],
        ]);

        $iuran->update($validated);

        ActivityLog::record(Auth::user(), ActivityLog::UPDATE, "Memperbarui iuran {$iuran->nama}.", $iuran);

        return redirect()->route('iuran.show', $iuran)->with('success', 'Iuran berhasil diperbarui.');
    }

    public function destroy(Iuran $iuran)
    {
        $nama = $iuran->nama;
        $transaksiIds = $iuran->pembayarans()->pluck('transaksi_id')->filter()->all();

        DB::transaction(function () use ($iuran, $transaksiIds) {
            $iuran->delete(); // cascade: peserta + pembayaran
            if ($transaksiIds !== []) {
                TransaksiKeuangan::whereIn('id', $transaksiIds)->delete();
            }
        });

        ActivityLog::record(Auth::user(), ActivityLog::DELETE, "Menghapus iuran {$nama} beserta ".count($transaksiIds).' catatan kas terkait.');

        return redirect()->route('iuran.index')->with('success', 'Iuran berhasil dihapus.');
    }

    /** Tambah peserta ke iuran berjalan (by pilih nama / hasil filter role/prodi). */
    public function tambahPeserta(Request $request, Iuran $iuran)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array|min:1|max:2000',
            'user_ids.*' => 'exists:users,id',
            'nominal_wajib' => 'nullable|integer|min:1|max:999999999999',
        ]);

        $nominal = $validated['nominal_wajib'] ?? $iuran->nominal;
        $baru = 0;
        foreach (array_unique($validated['user_ids']) as $uid) {
            $p = IuranPeserta::firstOrCreate(
                ['iuran_id' => $iuran->id, 'user_id' => $uid],
                ['nominal_wajib' => $nominal]
            );
            if ($p->wasRecentlyCreated) {
                $baru++;
            }
        }

        return back()->with('success', "{$baru} peserta ditambahkan ke {$iuran->nama}.");
    }

    /** Ubah nominal wajib per peserta (mis. dispensasi / beda angkatan). */
    public function updatePeserta(Request $request, Iuran $iuran, IuranPeserta $peserta)
    {
        abort_unless($peserta->iuran_id === $iuran->id, 404);
        $validated = $request->validate([
            'nominal_wajib' => 'required|integer|min:1|max:999999999999',
        ]);
        $sudah = (int) $peserta->pembayarans()->sum('jumlah');
        if ($validated['nominal_wajib'] < $sudah) {
            return back()->withErrors(['nominal_wajib' => 'Nominal tidak boleh lebih kecil dari yang sudah dibayar ('.number_format($sudah, 0, ',', '.').').']);
        }
        $peserta->update($validated);

        return back()->with('success', 'Nominal peserta diperbarui.');
    }

    public function hapusPeserta(Iuran $iuran, IuranPeserta $peserta)
    {
        abort_unless($peserta->iuran_id === $iuran->id, 404);
        $nama = $peserta->user?->name ?? 'peserta';
        $transaksiIds = $peserta->pembayarans()->pluck('transaksi_id')->filter()->all();

        DB::transaction(function () use ($peserta, $transaksiIds) {
            $peserta->delete();
            if ($transaksiIds !== []) {
                TransaksiKeuangan::whereIn('id', $transaksiIds)->delete();
            }
        });

        return back()->with('success', "{$nama} dihapus dari iuran.");
    }

    /**
     * Catat pembayaran (boleh cicil). Otomatis masuk kas sebagai pemasukan
     * agar saldo tetap satu sumber kebenaran.
     */
    public function bayar(Request $request, Iuran $iuran)
    {
        $validated = $request->validate([
            'peserta_id' => 'required|exists:iuran_pesertas,id',
            'jumlah' => 'required|integer|min:1000|max:999999999999',
            'tanggal' => 'required|date|before_or_equal:today',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $peserta = IuranPeserta::with(['user:id,name', 'pembayarans'])->findOrFail($validated['peserta_id']);
        abort_unless($peserta->iuran_id === $iuran->id, 404);

        $sudah = (int) $peserta->pembayarans->sum('jumlah');
        $sisa = (int) $peserta->nominal_wajib - $sudah;

        if ($sisa <= 0) {
            return back()->withErrors(['jumlah' => 'Peserta ini sudah lunas.']);
        }
        if ($validated['jumlah'] > $sisa) {
            return back()->withErrors(['jumlah' => 'Melebihi sisa tagihan ('.number_format($sisa, 0, ',', '.').').']);
        }

        DB::transaction(function () use ($validated, $peserta, $iuran) {
            $transaksi = TransaksiKeuangan::create([
                'tipe' => TransaksiKeuangan::TIPE_MASUK,
                'kategori' => 'Kas Anggota',
                'keterangan' => "Iuran {$iuran->nama} — {$peserta->user?->name}".($validated['keterangan'] ? " ({$validated['keterangan']})" : ''),
                'jumlah' => $validated['jumlah'],
                'tanggal' => $validated['tanggal'],
                'created_by' => Auth::id(),
            ]);

            IuranPembayaran::create([
                'iuran_peserta_id' => $peserta->id,
                'jumlah' => $validated['jumlah'],
                'tanggal' => $validated['tanggal'],
                'keterangan' => $validated['keterangan'] ?? null,
                'transaksi_id' => $transaksi->id,
                'created_by' => Auth::id(),
            ]);
        });

        ActivityLog::record(Auth::user(), ActivityLog::CREATE, "Mencatat iuran {$iuran->nama} Rp ".number_format($validated['jumlah'], 0, ',', '.')." a.n. {$peserta->user?->name}.");

        return back()->with('success', 'Pembayaran tercatat dan otomatis masuk kas.');
    }

    /** Hapus satu pembayaran (cicilan) + catatan kas terkait. */
    public function hapusBayar(Iuran $iuran, IuranPembayaran $pembayaran)
    {
        $peserta = $pembayaran->peserta;
        abort_unless($peserta && $peserta->iuran_id === $iuran->id, 404);

        DB::transaction(function () use ($pembayaran) {
            $transaksiId = $pembayaran->transaksi_id;
            $pembayaran->delete();
            if ($transaksiId) {
                TransaksiKeuangan::where('id', $transaksiId)->delete();
            }
        });

        return back()->with('success', 'Pembayaran dihapus, kas disesuaikan.');
    }
}
