<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\GelombangPendaftaran;
use App\Models\PendaftaranPengurus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PendaftaranPengurusController extends Controller
{
    /** Daftar milik mahasiswa yang sedang login. */
    public function mine()
    {
        $pendaftarans = PendaftaranPengurus::where('user_id', Auth::id())
            ->latest()
            ->get();

        return Inertia::render('Pendaftaran/Index', [
            'pendaftarans' => $pendaftarans,
            'user' => Auth::user(),
        ]);
    }

    /** Form daftar (khusus mahasiswa, prefill dari akun). */
    public function create()
    {
        $me = Auth::user();

        // Satu akun hanya boleh daftar satu kali per gelombang yang dibuka.
        // Kalau sudah ada, arahkan ke info pendaftaran, bukan form lagi.
        if ($saya = $this->pendaftaranAktifSaya()) {
            return redirect()->route('pendaftaran.show', $saya)
                ->with('info', 'Kamu sudah mendaftar di periode ini. Ini status pendaftaranmu.');
        }

        return Inertia::render('Pendaftaran/Create', [
            'user' => $me,
            'prodiList' => User::PRODI,
            'prefill' => [
                'nama' => $me->name,
                'nim' => $me->nim,
                'prodi' => $me->prodi,
                'semester' => $me->semester,
                'tempat_lahir' => $me->tempat_lahir,
                'tanggal_lahir' => $me->tanggal_lahir ? $me->tanggal_lahir->format('Y-m-d') : '',
                'email' => $me->email,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $gelombang = GelombangPendaftaran::yangDibuka();
        if (! $gelombang) {
            return back()->with('error', 'Pendaftaran sedang ditutup. Tunggu gelombang berikutnya.');
        }

        // Cegah submit ganda (double-click / buka form di dua tab).
        if ($saya = $this->pendaftaranAktifSaya($gelombang)) {
            return redirect()->route('pendaftaran.show', $saya)
                ->with('info', 'Kamu sudah mendaftar di periode ini.');
        }

        $this->normalizeRiwayatInput($request);
        $validated = $request->validate($this->rules(null, $gelombang->id));

        $validated['user_id'] = Auth::id();
        $validated['gelombang_id'] = $gelombang->id;
        $validated['status'] = 'pending';
        unset($validated['catatan_seleksi']);
        $validated['riwayat_sekolah'] = PendaftaranPengurus::normalizeList($validated['riwayat_sekolah'] ?? null);
        $validated['riwayat_madrasah'] = PendaftaranPengurus::normalizeList($validated['riwayat_madrasah'] ?? null);
        $validated['riwayat_organisasi'] = PendaftaranPengurus::normalizeList($validated['riwayat_organisasi'] ?? null);

        $pendaftaran = PendaftaranPengurus::create($validated);

        ActivityLog::record(Auth::user(), ActivityLog::PENDAFTARAN, "Mendaftar pengurus BEM periode {$gelombang->nama}.", $pendaftaran);

        return redirect()->route('pendaftaran.show', $pendaftaran)->with('success', 'Pendaftaran pengurus berhasil dikirim. Tunggu hasil seleksi.');
    }

    /**
     * Halaman publik Join With Us (tanpa sidebar/navbar).
     * Bisa dibuka semua orang; form hanya tampil bila gelombang dibuka
     * dan pengunjung login sebagai mahasiswa.
     */
    public function join()
    {
        $gelombang = GelombangPendaftaran::yangDibuka();
        $me = Auth::user();

        return Inertia::render('Join/Index', [
            'gelombang' => $gelombang ? [
                'id' => $gelombang->id,
                'nama' => $gelombang->nama,
                'deskripsi' => $gelombang->deskripsi,
                'tanggal_mulai' => $gelombang->tanggal_mulai?->format('d M Y'),
                'tanggal_selesai' => $gelombang->tanggal_selesai?->format('d M Y'),
            ] : null,
            'isOpen' => (bool) $gelombang,
            'user' => $me,
            'prodiList' => User::PRODI,
            // Kalau user sudah daftar di gelombang ini, kirim ringkasannya
            // agar halaman menampilkan info status, bukan form lagi.
            'pendaftaranSaya' => $me && $gelombang
                ? PendaftaranPengurus::where('user_id', $me->id)
                    ->where('gelombang_id', $gelombang->id)
                    ->latest()
                    ->first(['id', 'nama', 'nim', 'status', 'gelombang_id'])
                : null,
            'prefill' => $me ? [
                'nama' => $me->name,
                'nim' => $me->nim,
                'prodi' => $me->prodi,
                'semester' => $me->semester,
                'tempat_lahir' => $me->tempat_lahir,
                'tanggal_lahir' => $me->tanggal_lahir ? $me->tanggal_lahir->format('Y-m-d') : '',
                'email' => $me->email,
            ] : null,
        ]);
    }

    /** Submit dari halaman publik (wajib login mahasiswa via middleware). */
    public function joinStore(Request $request)
    {
        $gelombang = GelombangPendaftaran::yangDibuka();
        if (! $gelombang) {
            return back()->with('error', 'Pendaftaran sedang ditutup. Tunggu gelombang berikutnya.');
        }

        // Cegah submit ganda dari halaman publik.
        if ($saya = $this->pendaftaranAktifSaya($gelombang)) {
            return redirect()->route('join.sukses', $saya)
                ->with('info', 'Kamu sudah mendaftar di periode ini.');
        }

        $this->normalizeRiwayatInput($request);
        $validated = $request->validate($this->rules(null, $gelombang->id));

        $validated['user_id'] = Auth::id();
        $validated['gelombang_id'] = $gelombang->id;
        $validated['status'] = 'pending';
        unset($validated['catatan_seleksi']);
        $validated['riwayat_sekolah'] = PendaftaranPengurus::normalizeList($validated['riwayat_sekolah'] ?? null);
        $validated['riwayat_madrasah'] = PendaftaranPengurus::normalizeList($validated['riwayat_madrasah'] ?? null);
        $validated['riwayat_organisasi'] = PendaftaranPengurus::normalizeList($validated['riwayat_organisasi'] ?? null);

        $pendaftaran = PendaftaranPengurus::create($validated);

        ActivityLog::record(Auth::user(), ActivityLog::PENDAFTARAN, "Mendaftar pengurus BEM periode {$gelombang->nama} via Join With Us.", $pendaftaran);

        return redirect()->route('join.sukses', $pendaftaran)->with('success', 'Pendaftaran berhasil dikirim. Pantau statusmu di halaman ini.');
    }

    /** Halaman sukses publik setelah submit (milik sendiri). */
    public function joinSukses(PendaftaranPengurus $pendaftaran)
    {
        if ($pendaftaran->user_id !== Auth::id()) {
            abort(403, 'Kamu hanya bisa melihat pendaftaran milikmu sendiri.');
        }
        $pendaftaran->load('gelombang:id,nama');

        return Inertia::render('Join/Sukses', [
            'pendaftaran' => $pendaftaran,
            'user' => Auth::user(),
        ]);
    }

    /** Ubah status cepat (Terima/Tolak) khusus pengurus. */
    public function updateStatus(Request $request, PendaftaranPengurus $pendaftaran)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(PendaftaranPengurus::STATUS)],
            'catatan_seleksi' => 'nullable|string|max:1000',
        ]);

        $pendaftaran->update($validated);

        ActivityLog::record(Auth::user(), ActivityLog::STATUS, "Mengubah status pendaftar {$pendaftaran->nama} menjadi {$validated['status']}.", $pendaftaran);

        return back()->with('success', "Pendaftar {$pendaftaran->nama} ditandai: {$validated['status']}.");
    }

    /** Kelola semua pendaftar (khusus pengurus). */
    public function index(Request $request)
    {
        $query = PendaftaranPengurus::with(['user:id,name,nim', 'gelombang:id,nama'])->latest();

        $search = $request->input('search', '');
        $status = $request->input('status', '');
        $prodi = $request->input('prodi', '');

        if ($search !== '') {
            $query->where(fn ($q) => $q->where('nama', 'like', "%{$search}%")->orWhere('nim', 'like', "%{$search}%"));
        }
        if ($status !== '') {
            $query->where('status', $status);
        }
        if ($prodi !== '') {
            $query->where('prodi', $prodi);
        }

        $aktif = GelombangPendaftaran::yangDibuka();

        $gelombangs = GelombangPendaftaran::withCount('pendaftarans')
            ->latest()
            ->get()
            ->map(fn ($g) => [
                'id' => $g->id,
                'nama' => $g->nama,
                'deskripsi' => $g->deskripsi,
                'tanggal_mulai' => $g->tanggal_mulai?->format('Y-m-d'),
                'tanggal_selesai' => $g->tanggal_selesai?->format('Y-m-d'),
                'is_open' => (bool) $g->is_open,
                'sedang_dibuka' => $g->sedangDibuka(),
                'status_label' => $g->statusLabel(),
                'total_pendaftar' => $g->pendaftarans_count,
            ]);

        return Inertia::render('Admin/Pendaftaran/Index', [
            'pendaftarans' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['search', 'status', 'prodi']),
            'prodiList' => User::PRODI,
            'statusList' => PendaftaranPengurus::STATUS,
            'periodeAktif' => $aktif ? [
                'id' => $aktif->id,
                'nama' => $aktif->nama,
                'tanggal_mulai' => $aktif->tanggal_mulai?->format('d M Y'),
                'tanggal_selesai' => $aktif->tanggal_selesai?->format('d M Y'),
            ] : null,
            'gelombangs' => $gelombangs,
            'initialTab' => $request->input('tab', 'pendaftar'),
            'user' => Auth::user(),
        ]);
    }

    public function show(PendaftaranPengurus $pendaftaran)
    {
        $this->authorizeAccess($pendaftaran);
        $pendaftaran->load(['user:id,name,nim', 'gelombang:id,nama']);

        // Mahasiswa pakai halaman sendiri, pengurus pakai halaman admin.
        $component = Auth::user()->isPengurus() ? 'Admin/Pendaftaran/Show' : 'Pendaftaran/Show';

        return Inertia::render($component, [
            'pendaftaran' => $pendaftaran,
            'user' => Auth::user(),
        ]);
    }

    public function edit(PendaftaranPengurus $pendaftaran)
    {
        $this->authorizeAccess($pendaftaran);

        if (Auth::user()->isMahasiswa() && $pendaftaran->status !== 'pending') {
            return redirect()->route('pendaftaran.show', $pendaftaran)
                ->with('error', 'Pendaftaran yang sudah diproses tidak bisa diubah.');
        }

        $pendaftaran->load(['user:id,name,nim', 'gelombang:id,nama']);
        $component = Auth::user()->isPengurus() ? 'Admin/Pendaftaran/Edit' : 'Pendaftaran/Edit';

        return Inertia::render($component, [
            'pendaftaran' => $pendaftaran,
            'prodiList' => User::PRODI,
            'statusList' => PendaftaranPengurus::STATUS,
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request, PendaftaranPengurus $pendaftaran)
    {
        $this->authorizeAccess($pendaftaran);

        if (Auth::user()->isMahasiswa() && $pendaftaran->status !== 'pending') {
            return redirect()->route('pendaftaran.show', $pendaftaran)
                ->with('error', 'Pendaftaran yang sudah diproses tidak bisa diubah.');
        }

        $this->normalizeRiwayatInput($request);
        $validated = $request->validate($this->rules($pendaftaran));

        $validated['riwayat_sekolah'] = PendaftaranPengurus::normalizeList($validated['riwayat_sekolah'] ?? null);
        $validated['riwayat_madrasah'] = PendaftaranPengurus::normalizeList($validated['riwayat_madrasah'] ?? null);
        $validated['riwayat_organisasi'] = PendaftaranPengurus::normalizeList($validated['riwayat_organisasi'] ?? null);

        // Mahasiswa tidak boleh mengubah status / catatan seleksi milik pengurus.
        if (Auth::user()->isMahasiswa()) {
            unset($validated['status'], $validated['catatan_seleksi']);
        }

        $pendaftaran->update($validated);

        ActivityLog::record(Auth::user(), ActivityLog::UPDATE, "Memperbarui pendaftaran {$pendaftaran->nama}.", $pendaftaran);

        $redirect = Auth::user()->isPengurus()
            ? redirect()->route('pendaftaran.admin.index')
            : redirect()->route('pendaftaran.show', $pendaftaran);

        return $redirect->with('success', 'Pendaftaran berhasil diperbarui.');
    }

    public function destroy(PendaftaranPengurus $pendaftaran)
    {
        $this->authorizeAccess($pendaftaran);

        if (Auth::user()->isMahasiswa() && $pendaftaran->status !== 'pending') {
            return back()->with('error', 'Pendaftaran yang sudah diproses tidak bisa dihapus.');
        }

        $nama = $pendaftaran->nama;
        $pendaftaran->delete();

        ActivityLog::record(Auth::user(), ActivityLog::DELETE, "Menghapus pendaftaran {$nama}.");

        $redirect = Auth::user()->isPengurus()
            ? redirect()->route('pendaftaran.admin.index')
            : redirect()->route('pendaftaran.mine');

        return $redirect->with('success', 'Pendaftaran berhasil dihapus.');
    }

    private function authorizeAccess(PendaftaranPengurus $pendaftaran): void
    {
        $me = Auth::user();
        if ($me->isPengurus()) {
            return;
        }
        if ($pendaftaran->user_id !== $me->id) {
            abort(403, 'Kamu hanya bisa mengakses pendaftaran milikmu sendiri.');
        }
    }

    private function normalizeRiwayatInput(Request $request): void
    {
        foreach (['riwayat_sekolah', 'riwayat_madrasah', 'riwayat_organisasi'] as $key) {
            if ($request->has($key) && is_string($request->input($key))) {
                $normalized = PendaftaranPengurus::normalizeList($request->input($key));
                $request->merge([$key => $normalized ? explode("\n", $normalized) : []]);
            }
        }
    }

    /**
     * Pendaftaran milik user login pada gelombang yang sedang dibuka.
     * Aturan: satu akun = satu pendaftaran per periode.
     */
    private function pendaftaranAktifSaya(?GelombangPendaftaran $gelombang = null): ?PendaftaranPengurus
    {
        $me = Auth::user();
        if (! $me) {
            return null;
        }
        $gelombang ??= GelombangPendaftaran::yangDibuka();
        if (! $gelombang) {
            return null;
        }

        return PendaftaranPengurus::where('user_id', $me->id)
            ->where('gelombang_id', $gelombang->id)
            ->latest()
            ->first();
    }

    private function rules(?PendaftaranPengurus $pendaftaran = null, ?int $gelombangId = null): array
    {
        $isPengurus = Auth::user()?->isPengurus() ?? false;
        // Scope unik NIM per gelombang agar user bisa daftar lagi di periode berikutnya,
        // tapi tidak bisa dobel di periode yang sama.
        $gelombangId ??= $pendaftaran?->gelombang_id;

        $nimUnique = Rule::unique('pendaftaran_pengurus', 'nim')->ignore($pendaftaran?->id);
        if ($gelombangId) {
            $nimUnique->where(fn ($q) => $q->where('gelombang_id', $gelombangId));
        }

        $rules = [
            'nama' => 'required|string|max:255',
            'nim' => ['required', 'string', 'max:30', $nimUnique],
            'prodi' => ['required', Rule::in(User::PRODI)],
            'semester' => 'required|integer|min:1|max:14',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date|before_or_equal:today',
            'asrama' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'tujuan' => 'nullable|string|max:2000',
            'riwayat_sekolah' => 'nullable|array|max:30',
            'riwayat_sekolah.*' => 'nullable|string|max:255',
            'riwayat_madrasah' => 'nullable|array|max:30',
            'riwayat_madrasah.*' => 'nullable|string|max:255',
            'riwayat_organisasi' => 'nullable|array|max:30',
            'riwayat_organisasi.*' => 'nullable|string|max:255',
        ];

        if ($isPengurus) {
            $rules['status'] = ['required', Rule::in(PendaftaranPengurus::STATUS)];
            $rules['catatan_seleksi'] = 'nullable|string|max:1000';
        }

        return $rules;
    }
}
