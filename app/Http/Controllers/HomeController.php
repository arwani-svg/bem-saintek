<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\Informasi;
use App\Models\ProgramKerja;
use App\Models\Struktural;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $programs = ProgramKerja::with('departemen')
            ->where('is_unggulan', true)
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn (ProgramKerja $p) => $this->format($p));

        // Sekilas Informasi untuk beranda: 5 terbaru yang sudah dipublish.
        // Unggulan didahulukan agar sorotan admin ikut tampil di home.
        $informasis = Informasi::published()
            ->orderByDesc('is_unggulan')
            ->latest('tanggal_publikasi')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (Informasi $i) => $this->formatInformasi($i));

        return Inertia::render('Home/Index', [
            'programs' => $programs,
            'informasis' => $informasis->values(),
        ]);
    }

    public function struktural(Request $request)
    {
        $periods = Struktural::query()
            ->select('periode')
            ->distinct()
            ->orderByDesc('periode')
            ->pluck('periode');

        $periode = $request->input('periode');
        if (! $periode || ! $periods->contains($periode)) {
            $periode = $periods->first();
        }

        $items = $periode
            ? Struktural::with(['user', 'departemen'])
                ->where('periode', $periode)
                ->orderBy('urutan')
                ->orderBy('jabatan')
                ->get()
            : collect();

        $format = fn (Struktural $s) => [
            'id' => $s->id,
            'jabatan' => $s->jabatan,
            'urutan' => $s->urutan,
            'user' => [
                'id' => $s->user?->id,
                'name' => $s->user?->name,
                'nim' => $s->user?->nim,
                'prodi' => $s->user?->prodi,
                'angkatan' => $s->user?->angkatan,
                'jenis_kelamin' => $s->user?->jenis_kelamin,
                'foto_url' => $s->user?->foto_url,
            ],
            'departemen' => $s->departemen ? [
                'id' => $s->departemen->id,
                'nama' => $s->departemen->nama,
                'kepanjangan' => $s->departemen->kepanjangan,
                'icon' => $s->departemen->icon,
            ] : null,
        ];

        $pengurusHarian = $items
            ->whereNull('departemen_id')
            ->map($format)
            ->values();

        $departemens = $items
            ->whereNotNull('departemen_id')
            ->groupBy('departemen_id')
            ->map(function ($group) use ($format) {
                $departemen = $group->first()->departemen;

                return [
                    'id' => $departemen->id,
                    'nama' => $departemen->nama,
                    'kepanjangan' => $departemen->kepanjangan,
                    'icon' => $departemen->icon,
                    'deskripsi' => $departemen->deskripsi,
                    'pengurus' => $group->map($format)->values(),
                ];
            })
            ->sortBy('nama')
            ->values();

        return Inertia::render('Profile/Struktural', [
            'periods' => $periods,
            'selectedPeriode' => $periode,
            'pengurusHarian' => $pengurusHarian,
            'departemens' => $departemens,
            'totalPengurus' => $items->count(),
        ]);
    }

    public function departemen(Departemen $departemen)
    {
        $departemen->load(['programKerjas' => fn ($q) => $q->with('departemen')->latest()]);

        $prokers = $departemen->programKerjas->map(fn (ProgramKerja $p) => $this->format($p, true));

        return Inertia::render('Departemen/Show', [
            'departemen' => [
                'id' => $departemen->id,
                'nama' => $departemen->nama,
                'kepanjangan' => $departemen->kepanjangan,
                'deskripsi' => $departemen->deskripsi,
                'tujuan' => $departemen->tujuan,
                'icon' => $departemen->icon,
                'total_proker' => $prokers->count(),
                'proker_berlangsung' => $prokers->where('status', ProgramKerja::STATUS['berlangsung'])->count(),
                'proker_selesai' => $prokers->where('status', ProgramKerja::STATUS['selesai'])->count(),
            ],
            'prokers' => $prokers->values(),
            'lainnya' => Departemen::orderBy('nama')->where('id', '!=', $departemen->id)->get(['id', 'nama', 'icon']),
        ]);
    }

    public function program(Request $request)
    {
        $query = ProgramKerja::with('departemen');

        $search = $request->input('search', '');
        $departemenId = $request->input('departemen_id', '');
        $status = $request->input('status', '');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        if ($departemenId !== '') {
            $query->where('departemen_id', $departemenId);
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        $programs = $query->latest()->paginate(9)->withQueryString();

        // Format tiap item agar siap tampil, tanpa mengubah struktur pagination
        $programs->getCollection()->transform(fn (ProgramKerja $p) => $this->format($p, true));

        return Inertia::render('Program/Index', [
            'programs' => $programs,
            'filters' => $request->only(['search', 'departemen_id', 'status']),
            'departemens' => Departemen::orderBy('nama')->get(['id', 'nama']),
            'statusList' => ProgramKerja::STATUS,
        ]);
    }

    /**
     * Halaman publik daftar Informasi (berita, infografis, penghargaan, lomba).
     */
    public function informasi(Request $request)
    {
        $query = Informasi::published();

        $search = $request->input('search', '');
        $jenis = $request->input('jenis', '');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('ringkasan', 'like', "%{$search}%");
            });
        }

        if ($jenis !== '' && array_key_exists($jenis, Informasi::JENIS)) {
            $query->where('jenis', $jenis);
        }

        $items = $query->latest('tanggal_publikasi')->latest()->paginate(9)->withQueryString();
        $items->getCollection()->transform(fn (Informasi $i) => $this->formatInformasi($i));

        // Sorotan unggulan untuk hero/list teratas
        $unggulan = Informasi::published()->where('is_unggulan', true)
            ->latest('tanggal_publikasi')->limit(3)
            ->get()->map(fn (Informasi $i) => $this->formatInformasi($i));

        return Inertia::render('Informasi/Index', [
            'items' => $items,
            'unggulan' => $unggulan,
            'filters' => $request->only(['search', 'jenis']),
            'jenisList' => Informasi::JENIS,
            'counts' => [
                'semua' => Informasi::published()->count(),
                'berita' => Informasi::published()->where('jenis', 'berita')->count(),
                'infografis' => Informasi::published()->where('jenis', 'infografis')->count(),
                'penghargaan' => Informasi::published()->where('jenis', 'penghargaan')->count(),
                'lomba' => Informasi::published()->where('jenis', 'lomba')->count(),
            ],
        ]);
    }

    /**
     * Halaman publik detail satu Informasi berdasarkan slug.
     */
    public function informasiShow(string $slug)
    {
        $informasi = Informasi::published()->where('slug', $slug)->firstOrFail();

        $terkait = Informasi::published()
            ->where('id', '!=', $informasi->id)
            ->where('jenis', $informasi->jenis)
            ->latest('tanggal_publikasi')->limit(3)
            ->get()->map(fn (Informasi $i) => $this->formatInformasi($i));

        // Jika tidak ada yang sejenis, ambil terbaru apa pun
        if ($terkait->isEmpty()) {
            $terkait = Informasi::published()
                ->where('id', '!=', $informasi->id)
                ->latest('tanggal_publikasi')->limit(3)
                ->get()->map(fn (Informasi $i) => $this->formatInformasi($i));
        }

        return Inertia::render('Informasi/Show', [
            'item' => $this->formatInformasi($informasi, true),
            'terkait' => $terkait->values(),
            'jenisList' => Informasi::JENIS,
        ]);
    }

    private function formatInformasi(Informasi $i, bool $detail = false): array
    {
        $data = [
            'id' => $i->id,
            'jenis' => $i->jenis,
            'jenis_label' => $i->jenis_label,
            'is_artikel' => $i->isArtikel(),
            'judul' => $i->judul,
            'slug' => $i->slug,
            'ringkasan' => $i->ringkasan,
            'gambar_url' => $i->gambar_url,
            'tanggal' => $i->tanggal_publikasi?->format('d M Y'),
            'is_unggulan' => $i->is_unggulan,
        ];

        if ($detail) {
            $data['konten'] = $i->konten;
        }

        return $data;
    }

    private function format(ProgramKerja $p, bool $detail = false): array
    {
        $data = [
            'id' => $p->id,
            'category' => $p->kategori ?: $p->departemen->nama,
            'departemen' => $p->departemen->nama,
            'title' => $p->judul,
            'description' => $p->deskripsi,
            'status' => ProgramKerja::STATUS[$p->status] ?? $p->status,
            'color' => $p->warna,
        ];

        if ($detail) {
            $data += [
                'lokasi' => $p->lokasi,
                'penanggung_jawab' => $p->penanggung_jawab,
                'tanggal_mulai' => $p->tanggal_mulai?->format('d M Y'),
                'tanggal_selesai' => $p->tanggal_selesai?->format('d M Y'),
            ];
        }

        return $data;
    }
}
