<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\TransaksiKeuangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class KeuanganController extends Controller
{
    public function index(Request $request)
    {
        $query = TransaksiKeuangan::with('pencatat:id,name')->latest('tanggal')->latest('id');
        $this->applyFilters($query, $request);

        $masuk = (clone $query)->where('tipe', TransaksiKeuangan::TIPE_MASUK)->sum('jumlah');
        $keluar = (clone $query)->where('tipe', TransaksiKeuangan::TIPE_KELUAR)->sum('jumlah');

        return Inertia::render('Admin/Keuangan/Index', [
            'transaksis' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['search', 'tipe', 'kategori', 'bulan', 'dari', 'sampai']),
            'ringkasan' => [
                'masuk' => (int) $masuk,
                'keluar' => (int) $keluar,
                'selisih' => (int) $masuk - (int) $keluar,
                'saldo' => TransaksiKeuangan::saldo(),
                'total_masuk' => TransaksiKeuangan::totalMasuk(),
                'total_keluar' => TransaksiKeuangan::totalKeluar(),
            ],
            'kategoriMasuk' => TransaksiKeuangan::KATEGORI_MASUK,
            'kategoriKeluar' => TransaksiKeuangan::KATEGORI_KELUAR,
            'user' => Auth::user(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Keuangan/Create', [
            'kategoriMasuk' => TransaksiKeuangan::KATEGORI_MASUK,
            'kategoriKeluar' => TransaksiKeuangan::KATEGORI_KELUAR,
            'user' => Auth::user(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());
        $validated['kategori'] = $this->resolveKategori($request, $validated['kategori']);
        $validated['created_by'] = Auth::id();

        TransaksiKeuangan::create($validated);

        ActivityLog::record(Auth::user(), ActivityLog::CREATE, "Mencatat transaksi {$validated['tipe']} Rp ".number_format($validated['jumlah'], 0, ',', '.')." ({$validated['kategori']}).");

        return redirect()->route('keuangan.index')->with('success', 'Transaksi berhasil dicatat.');
    }

    public function edit(TransaksiKeuangan $keuangan)
    {
        return Inertia::render('Admin/Keuangan/Edit', [
            'transaksi' => $keuangan,
            'kategoriMasuk' => TransaksiKeuangan::KATEGORI_MASUK,
            'kategoriKeluar' => TransaksiKeuangan::KATEGORI_KELUAR,
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request, TransaksiKeuangan $keuangan)
    {
        $validated = $request->validate($this->rules());
        $validated['kategori'] = $this->resolveKategori($request, $validated['kategori']);

        $keuangan->update($validated);

        ActivityLog::record(Auth::user(), ActivityLog::UPDATE, "Memperbarui transaksi {$keuangan->tipe} Rp ".number_format($keuangan->jumlah, 0, ',', '.')." ({$keuangan->kategori}).", $keuangan);

        return redirect()->route('keuangan.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(TransaksiKeuangan $keuangan)
    {
        $ringkas = "transaksi {$keuangan->tipe} Rp ".number_format($keuangan->jumlah, 0, ',', '.')." ({$keuangan->kategori})";
        $keuangan->delete();

        ActivityLog::record(Auth::user(), ActivityLog::DELETE, "Menghapus {$ringkas}.");

        return redirect()->route('keuangan.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    /**
     * Rekap keuangan — filter bulan/tanggal, ringkas per kategori,
     * siap cetak (tombol print di halaman).
     */
    public function rekap(Request $request)
    {
        $query = TransaksiKeuangan::query();
        $this->applyFilters($query, $request);

        $items = $query->orderBy('tanggal')->orderBy('id')->get();

        $perKategori = $items->groupBy(fn ($t) => $t->tipe.':'.$t->kategori)
            ->map(fn ($rows, $key) => [
                'tipe' => $rows->first()->tipe,
                'kategori' => $rows->first()->kategori,
                'transaksi' => $rows->count(),
                'total' => (int) $rows->sum('jumlah'),
            ])
            ->sortBy([['tipe', 'asc'], ['total', 'desc']])
            ->values();

        $masuk = (int) $items->where('tipe', TransaksiKeuangan::TIPE_MASUK)->sum('jumlah');
        $keluar = (int) $items->where('tipe', TransaksiKeuangan::TIPE_KELUAR)->sum('jumlah');

        return Inertia::render('Admin/Keuangan/Rekap', [
            'items' => $items,
            'perKategori' => $perKategori,
            'filters' => $request->only(['tipe', 'bulan', 'dari', 'sampai']),
            'ringkasan' => [
                'masuk' => $masuk,
                'keluar' => $keluar,
                'selisih' => $masuk - $keluar,
                'periode' => $this->labelPeriode($request),
            ],
            'user' => Auth::user(),
        ]);
    }

    private function applyFilters($query, Request $request): void
    {
        if ($search = $request->input('search', '')) {
            $query->where(function ($q) use ($search) {
                $q->where('keterangan', 'like', "%{$search}%")
                    ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->input('tipe'));
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->input('kategori'));
        }

        // Filter bulan (format YYYY-MM) — diabaikan bila rentang tanggal diisi.
        if ($request->filled('bulan') && ! $request->filled('dari') && ! $request->filled('sampai')) {
            [$tahun, $bulan] = array_pad(explode('-', $request->input('bulan')), 2, null);
            if (is_numeric($tahun) && is_numeric($bulan)) {
                $query->whereYear('tanggal', (int) $tahun)->whereMonth('tanggal', (int) $bulan);
            }
        }

        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->input('dari'));
        }

        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->input('sampai'));
        }
    }

    private function labelPeriode(Request $request): string
    {
        if ($request->filled('dari') || $request->filled('sampai')) {
            $dari = $request->input('dari', 'awal');
            $sampai = $request->input('sampai', 'sekarang');

            return "{$dari} s/d {$sampai}";
        }

        if ($request->filled('bulan')) {
            return date('F Y', strtotime($request->input('bulan').'-01'));
        }

        return 'Semua waktu';
    }

    /**
     * Pilihan "Lainnya" + ketikan bebas -> simpan ketikan sebagai kategori.
     */
    private function resolveKategori(Request $request, string $kategori): string
    {
        if ($kategori === 'Lainnya' && trim((string) $request->input('kategori_lain', '')) !== '') {
            return mb_substr(trim((string) $request->input('kategori_lain')), 0, 100);
        }

        return $kategori;
    }

    private function rules(): array
    {
        return [
            'tipe' => ['required', Rule::in(TransaksiKeuangan::TIPE)],
            'kategori' => 'required|string|max:100',
            'kategori_lain' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string|max:255',
            'jumlah' => 'required|integer|min:1|max:999999999999',
            'tanggal' => 'required|date|before_or_equal:today',
        ];
    }
}
