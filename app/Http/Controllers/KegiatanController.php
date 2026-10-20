<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Kegiatan;
use App\Models\KegiatanPeserta;
use App\Models\Kehadiran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class KegiatanController extends Controller
{
    public function index(Request $request)
    {
        $query = Kegiatan::withCount('pesertas')->latest('tanggal')->latest('id');

        if ($search = $request->input('search', '')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $kegiatans = $query->paginate(10)->withQueryString();

        $hadirMap = Kehadiran::whereIn('kegiatan_peserta_id', function ($q) use ($kegiatans) {
            $q->select('id')->from('kegiatan_pesertas')->whereIn('kegiatan_id', $kegiatans->pluck('id'));
        })
            ->selectRaw('kegiatan_pesertas.kegiatan_id as kid, kehadirans.status, COUNT(*) as total')
            ->join('kegiatan_pesertas', 'kehadirans.kegiatan_peserta_id', '=', 'kegiatan_pesertas.id')
            ->groupBy('kegiatan_pesertas.kegiatan_id', 'kehadirans.status')
            ->get()
            ->groupBy('kid');

        $kegiatans->getCollection()->transform(function ($k) use ($hadirMap) {
            $rows = $hadirMap[$k->id] ?? collect();
            $hitung = fn ($s) => (int) ($rows->firstWhere('status', $s)->total ?? 0);
            $k->hadir = $hitung(Kehadiran::HADIR);
            $k->izin = $hitung(Kehadiran::IZIN);
            $k->sakit = $hitung(Kehadiran::SAKIT);
            $k->alpa = $hitung(Kehadiran::ALPA);
            $k->ditandai = $k->hadir + $k->izin + $k->sakit + $k->alpa;
            $k->belum = max(0, (int) $k->pesertas_count - $k->ditandai);

            return $k;
        });

        return Inertia::render('Admin/Kegiatan/Index', [
            'kegiatans' => $kegiatans,
            'filters' => $request->only(['search', 'status']),
            'statusList' => Kegiatan::STATUS,
            'user' => Auth::user(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Kegiatan/Create', [
            'prodiList' => User::PRODI,
            'rolesList' => Role::orderBy('name')->pluck('name'),
            'semesterList' => User::SEMESTER_LIST,
            'user' => Auth::user(),
        ]);
    }

    /** Endpoint JSON untuk picker peserta: ?search=&prodi=&role=&semester=&limit=20 */
    public function cariUser(Request $request)
    {
        $query = User::query()->select(['id', 'name', 'nim', 'foto', 'prodi', 'angkatan', 'role', 'status']);

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
        if ($request->filled('semester')) {
            // Semester berjalan dihitung dari angkatan — petakan ke angkatan.
            $dipilih = array_map('intval', (array) $request->input('semester'));
            $angkatans = User::angkatanUntukSemesterBanyak($dipilih);
            $query->whereIn('angkatan', $angkatans);
        }
        if ($request->filled('except_kegiatan')) {
            $sudah = KegiatanPeserta::where('kegiatan_id', $request->input('except_kegiatan'))->pluck('user_id');
            $query->whereNotIn('id', $sudah);
        }

        return response()->json(
            $query->orderBy('name')->limit(min(50, (int) $request->input('limit', 20)))->get()
                ->map(fn (User $u) => [
                    'id' => $u->id,
                    'name' => $u->name,
                    'nim' => $u->nim,
                    'foto_url' => $u->foto_url,
                    'prodi' => $u->prodi,
                    'angkatan' => $u->angkatan,
                    'semester' => $u->semester,
                    'role' => $u->role,
                    'status' => $u->status,
                ])
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'deskripsi' => 'nullable|string|max:1000',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'nullable|string|max:5',
            'lokasi' => 'nullable|string|max:150',
            'poin_alpa' => 'required|integer|min:0|max:1000',
            'peserta_ids' => 'required|array|min:1|max:2000',
            'peserta_ids.*' => 'exists:users,id',
        ]);

        $kegiatan = DB::transaction(function () use ($validated) {
            $kegiatan = Kegiatan::create([
                'nama' => $validated['nama'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'tanggal' => $validated['tanggal'],
                'waktu_mulai' => $validated['waktu_mulai'] ?? null,
                'lokasi' => $validated['lokasi'] ?? null,
                'poin_alpa' => $validated['poin_alpa'],
                'status' => Kegiatan::STATUS_TERJADWAL,
                'created_by' => Auth::id(),
            ]);

            foreach (array_unique($validated['peserta_ids']) as $uid) {
                KegiatanPeserta::firstOrCreate(
                    ['kegiatan_id' => $kegiatan->id, 'user_id' => $uid]
                );
            }

            return $kegiatan;
        });

        ActivityLog::record(Auth::user(), ActivityLog::CREATE, "Membuat kegiatan {$kegiatan->nama} untuk ".count($validated['peserta_ids']).' peserta.');

        return redirect()->route('kegiatan.show', $kegiatan)->with('success', 'Kegiatan berhasil dibuat.');
    }

    /** Detail kegiatan: status tiap peserta + ringkasan. */
    public function show(Request $request, Kegiatan $kegiatan)
    {
        $pesertaQuery = $kegiatan->pesertas()->with(['user:id,name,nim,foto,prodi,role', 'kehadiran']);

        if ($search = $request->input('search', '')) {
            $pesertaQuery->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('nim', 'like', "%{$search}%"));
        }
        if ($request->filled('prodi')) {
            $pesertaQuery->whereHas('user', fn ($q) => $q->where('prodi', $request->input('prodi')));
        }

        $semua = $pesertaQuery->get();

        $rows = $semua->map(fn (KegiatanPeserta $p) => [
            'id' => $p->id,
            'user_id' => $p->user_id,
            'status' => $p->kehadiran?->status, // null = belum ditandai
            'keterangan' => $p->kehadiran?->keterangan,
            'user' => $p->user,
        ]);

        if ($request->filled('status_hadir')) {
            $f = $request->input('status_hadir');
            $rows = $rows->filter(fn ($r) => ($r['status'] ?? 'belum') === $f)->values();
        }

        return Inertia::render('Admin/Kegiatan/Show', [
            'kegiatan' => $kegiatan,
            'pesertas' => $rows->values(),
            'filters' => $request->only(['search', 'prodi', 'status_hadir']),
            'prodiList' => User::PRODI,
            'ringkasan' => $this->ringkasan($kegiatan),
            'user' => Auth::user(),
        ]);
    }

    /**
     * Halaman absen cepat (mode terminal): peserta TIDAK dimuat sekaligus
     * agar RAM aman untuk kegiatan besar — saran diambil lewat
     * cariPeserta() per ketikan (debounce), max 12 baris.
     * Kunjungan pertama otomatis mengubah status terjadwal -> berlangsung.
     */
    public function absen(Kegiatan $kegiatan)
    {
        if ($kegiatan->status === Kegiatan::STATUS_TERJADWAL) {
            $kegiatan->update(['status' => Kegiatan::STATUS_BERLANGSUNG]);
        }

        return Inertia::render('Admin/Kegiatan/Absen', [
            'kegiatan' => $kegiatan->fresh(),
            'ringkasan' => $this->ringkasan($kegiatan->fresh()),
            'user' => Auth::user(),
        ]);
    }

    /**
     * Autocomplete server untuk halaman absen cepat: ?q=arw&limit=8.
     * Query kosong = antrian yang belum ditandai (diurut paling atas).
     */
    public function cariPeserta(Kegiatan $kegiatan, Request $request)
    {
        $limit = min(12, max(1, (int) $request->input('limit', 8)));
        $q = trim((string) $request->input('q', ''));

        $query = $kegiatan->pesertas()->with(['user:id,name,nim,foto,prodi', 'kehadiran']);

        if ($q !== '') {
            $query->whereHas('user', fn ($uq) => $uq
                ->where('name', 'like', "%{$q}%")
                ->orWhere('nim', 'like', "%{$q}%"));
        }

        $rows = $query->get()
            ->sortBy([
                fn ($p) => $p->kehadiran ? 1 : 0, // belum ditandai dulu
                fn ($p) => strtolower($p->user?->name ?? ''),
            ])
            ->take($limit)
            ->values()
            ->map(fn (KegiatanPeserta $p) => [
                'id' => $p->id,
                'status' => $p->kehadiran?->status,
                'user' => $p->user,
            ]);

        return response()->json($rows);
    }

    /**
     * Tandai satu peserta (upsert). Dipakai halaman absen cepat via fetch JSON
     * maupun form biasa (redirect back).
     */
    public function tandai(Request $request, Kegiatan $kegiatan)
    {
        $validated = $request->validate([
            'peserta_id' => 'required|exists:kegiatan_pesertas,id',
            'status' => ['required', Rule::in(Kehadiran::STATUS)],
            'keterangan' => 'nullable|string|max:255',
        ]);

        $peserta = KegiatanPeserta::with('user:id,name,foto')->findOrFail($validated['peserta_id']);
        abort_unless($peserta->kegiatan_id === $kegiatan->id, 404);

        Kehadiran::updateOrCreate(
            ['kegiatan_peserta_id' => $peserta->id],
            ['status' => $validated['status'], 'keterangan' => $validated['keterangan'] ?? null, 'dicatat_oleh' => Auth::id()]
        );

        // Sengaja tanpa ActivityLog per ketukan agar log tidak banjir
        // saat absen ratusan peserta (aksi bulk tetap dicatat).

        if ($request->wantsJson()) {
            return response()->json([
                'peserta_id' => $peserta->id,
                'status' => $validated['status'],
                'nama' => $peserta->user?->name,
                'foto_url' => $peserta->user?->foto_url,
                'ringkasan' => $this->ringkasan($kegiatan->fresh()),
            ]);
        }

        return back()->with('success', "{$peserta->user?->name} ditandai {$validated['status']}.");
    }

    /** Tandai semua yang belum ditandai sebagai alpa (saat menutup absen). */
    public function tandaiSisaAlpa(Request $request, Kegiatan $kegiatan)
    {
        $sudah = $kegiatan->kehadirans()->pluck('kegiatan_peserta_id')->all();
        $sisa = $kegiatan->pesertas()->whereNotIn('id', $sudah)->pluck('id');

        foreach ($sisa as $pid) {
            Kehadiran::create([
                'kegiatan_peserta_id' => $pid,
                'status' => Kehadiran::ALPA,
                'keterangan' => 'Otomatis: tidak hadir tanpa keterangan',
                'dicatat_oleh' => Auth::id(),
            ]);
        }

        ActivityLog::record(Auth::user(), ActivityLog::UPDATE, "Menandai {$sisa->count()} peserta tersisa sebagai alpa di {$kegiatan->nama}.", $kegiatan);

        if ($request->wantsJson()) {
            return response()->json(['ditandai' => $sisa->count(), 'ringkasan' => $this->ringkasan($kegiatan->fresh())]);
        }

        return back()->with('success', "{$sisa->count()} peserta ditandai alpa.");
    }

    /**
     * Rekap skor global: agregat per user lintas kegiatan
     * (alpa = poin_alpa per kegiatan, izin/sakit/hadir = 0).
     */
    public function rekap(Request $request)
    {
        $data = $this->dataRekap($request);

        return Inertia::render('Admin/Absensi/Rekap', [
            'rows' => $data['rows'],
            'filters' => $request->only(['search', 'prodi', 'semester', 'dari', 'sampai']),
            'prodiList' => User::PRODI,
            'semesterList' => User::SEMESTER_LIST,
            'totalKegiatan' => $data['totalKegiatan'],
            'user' => Auth::user(),
        ]);
    }

    /**
     * Detail rekap per orang: ringkasan + daftar tiap kegiatan yang diikuti
     * beserta statusnya (hadir/izin/sakit/alpa/belum) dan poinnya.
     */
    public function rekapDetail(Request $request, User $user)
    {
        $pesertas = KegiatanPeserta::with([
            'kegiatan:id,nama,tanggal,poin_alpa,status',
            'kehadiran',
        ])
            ->where('user_id', $user->id)
            ->whereHas('kegiatan', function ($q) use ($request) {
                $q->where('status', '!=', Kegiatan::STATUS_DIBATALKAN);
                if ($request->filled('dari')) {
                    $q->whereDate('tanggal', '>=', $request->input('dari'));
                }
                if ($request->filled('sampai')) {
                    $q->whereDate('tanggal', '<=', $request->input('sampai'));
                }
            })
            ->get()
            ->sortByDesc(fn ($p) => $p->kegiatan?->tanggal?->format('Y-m-d').'-'.$p->kegiatan?->id)
            ->values();

        $ringkasan = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0, 'belum' => 0, 'poin' => 0, 'kegiatan' => 0];
        $rows = $pesertas->map(function (KegiatanPeserta $p) use (&$ringkasan) {
            $status = $p->kehadiran?->status ?? 'belum';
            $poin = $status === Kehadiran::ALPA ? (int) ($p->kegiatan?->poin_alpa ?? 0) : 0;
            $ringkasan[$status]++;
            $ringkasan['poin'] += $poin;
            $ringkasan['kegiatan']++;

            return [
                'kegiatan_id' => $p->kegiatan?->id,
                'nama' => $p->kegiatan?->nama ?? '(kegiatan dihapus)',
                'tanggal' => $p->kegiatan?->tanggal?->format('Y-m-d'),
                'status_kegiatan' => $p->kegiatan?->status,
                'status' => $status,
                'keterangan' => $p->kehadiran?->keterangan,
                'poin' => $poin,
            ];
        });

        return Inertia::render('Admin/Absensi/RekapDetail', [
            'detailUser' => [
                'id' => $user->id,
                'nama' => $user->name,
                'nim' => $user->nim,
                'foto_url' => $user->foto_url,
                'prodi' => $user->prodi,
                'angkatan' => $user->angkatan,
                'semester' => $user->semester,
            ],
            'ringkasan' => $ringkasan,
            'rows' => $rows,
            'filters' => $request->only(['dari', 'sampai']),
            'user' => Auth::user(),
        ]);
    }

    /**
     * Ekspor rekap ke CSV (kompatibel Excel: BOM + delimiter ;).
     * Filter sama persis dengan halaman rekap.
     */
    public function rekapExport(Request $request)
    {
        $data = $this->dataRekap($request);
        $rows = $data['rows'];
        $namaFile = 'rekap-absensi-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            // BOM agar Excel Windows membaca UTF-8 dengan benar.
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['No', 'Nama', 'NIM', 'Prodi', 'Angkatan', 'Semester', 'Hadir', 'Izin', 'Sakit', 'Alpa', 'Total Poin', 'Jml Kegiatan'], ';');
            foreach ($rows as $i => $r) {
                fputcsv($out, [
                    $i + 1,
                    $r['nama'],
                    $r['nim'],
                    $r['prodi'],
                    $r['angkatan'],
                    $r['semester'],
                    $r['hadir'],
                    $r['izin'],
                    $r['sakit'],
                    $r['alpa'],
                    $r['poin'],
                    $r['kegiatan'],
                ], ';');
            }
            fclose($out);
        }, $namaFile, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Agregat rekap dipakai bersama oleh halaman rekap & ekspor CSV.
     *
     * @return array{rows: \Illuminate\Support\Collection, totalKegiatan: int}
     */
    private function dataRekap(Request $request): array
    {
        $kegiatanQuery = Kegiatan::where('status', '!=', Kegiatan::STATUS_DIBATALKAN);
        if ($request->filled('dari')) {
            $kegiatanQuery->whereDate('tanggal', '>=', $request->input('dari'));
        }
        if ($request->filled('sampai')) {
            $kegiatanQuery->whereDate('tanggal', '<=', $request->input('sampai'));
        }
        $kegiatanIds = $kegiatanQuery->pluck('id');

        $hadir = Kehadiran::with(['peserta.user:id,name,nim,foto,prodi,angkatan', 'peserta.kegiatan:id,nama,tanggal,poin_alpa'])
            ->whereHas('peserta', fn ($q) => $q->whereIn('kegiatan_id', $kegiatanIds))
            ->get();

        $semesterDipilih = $request->filled('semester') ? (int) $request->input('semester') : null;

        // Filter by user di memori (-search & prodi & semester) agar agregat tetap sederhana.
        $agregat = [];
        foreach ($hadir as $h) {
            $u = $h->peserta->user;
            if (! $u) {
                continue;
            }
            if (($search = $request->input('search', '')) !== '' && ! str_contains(strtolower($u->name.' '.$u->nim), strtolower($search))) {
                continue;
            }
            if ($request->filled('prodi') && $u->prodi !== $request->input('prodi')) {
                continue;
            }
            if (! is_null($semesterDipilih) && $u->semester !== $semesterDipilih) {
                continue;
            }
            $key = $u->id;
            $agregat[$key] ??= [
                'user_id' => $u->id, 'nama' => $u->name, 'nim' => $u->nim, 'foto_url' => $u->foto_url, 'prodi' => $u->prodi,
                'angkatan' => $u->angkatan, 'semester' => $u->semester,
                'hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0, 'poin' => 0, 'kegiatan' => 0,
            ];
            $agregat[$key][$h->status]++;
            $agregat[$key]['kegiatan']++;
            if ($h->status === Kehadiran::ALPA) {
                $agregat[$key]['poin'] += (int) ($h->peserta->kegiatan?->poin_alpa ?? 0);
            }
        }

        $rows = collect($agregat)->sortBy([['poin', 'desc'], ['alpa', 'desc'], ['nama', 'asc']])->values();

        return ['rows' => $rows, 'totalKegiatan' => $kegiatanIds->count()];
    }

    public function edit(Kegiatan $kegiatan)
    {
        return Inertia::render('Admin/Kegiatan/Edit', [
            'kegiatan' => $kegiatan,
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'deskripsi' => 'nullable|string|max:1000',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'nullable|string|max:5',
            'lokasi' => 'nullable|string|max:150',
            'poin_alpa' => 'required|integer|min:0|max:1000',
            'status' => ['required', Rule::in(Kegiatan::STATUS)],
        ]);

        $kegiatan->update($validated);

        ActivityLog::record(Auth::user(), ActivityLog::UPDATE, "Memperbarui kegiatan {$kegiatan->nama}.", $kegiatan);

        return redirect()->route('kegiatan.show', $kegiatan)->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        $nama = $kegiatan->nama;
        $kegiatan->delete(); // cascade: peserta + kehadiran

        ActivityLog::record(Auth::user(), ActivityLog::DELETE, "Menghapus kegiatan {$nama} beserta data kehadirannya.");

        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil dihapus.');
    }

    public function tambahPeserta(Request $request, Kegiatan $kegiatan)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array|min:1|max:2000',
            'user_ids.*' => 'exists:users,id',
        ]);

        $baru = 0;
        foreach (array_unique($validated['user_ids']) as $uid) {
            $p = KegiatanPeserta::firstOrCreate(
                ['kegiatan_id' => $kegiatan->id, 'user_id' => $uid]
            );
            if ($p->wasRecentlyCreated) {
                $baru++;
            }
        }

        return back()->with('success', "{$baru} peserta ditambahkan ke {$kegiatan->nama}.");
    }

    public function hapusPeserta(Kegiatan $kegiatan, KegiatanPeserta $peserta)
    {
        abort_unless($peserta->kegiatan_id === $kegiatan->id, 404);
        $nama = $peserta->user?->name ?? 'peserta';
        $peserta->delete(); // cascade: kehadiran ikut hapus

        return back()->with('success', "{$nama} dihapus dari kegiatan.");
    }

    private function ringkasan(Kegiatan $kegiatan): array
    {
        $total = $kegiatan->pesertas()->count();
        $perStatus = $kegiatan->kehadirans()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $hadir = (int) ($perStatus[Kehadiran::HADIR] ?? 0);
        $izin = (int) ($perStatus[Kehadiran::IZIN] ?? 0);
        $sakit = (int) ($perStatus[Kehadiran::SAKIT] ?? 0);
        $alpa = (int) ($perStatus[Kehadiran::ALPA] ?? 0);
        $ditandai = $hadir + $izin + $sakit + $alpa;

        return [
            'total' => $total,
            'hadir' => $hadir,
            'izin' => $izin,
            'sakit' => $sakit,
            'alpa' => $alpa,
            'belum' => max(0, $total - $ditandai),
            'poin_terbit' => $alpa * (int) $kegiatan->poin_alpa,
            'progress' => $total > 0 ? round($ditandai / $total * 100) : 0,
        ];
    }
}
