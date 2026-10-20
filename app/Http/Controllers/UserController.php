<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\FotoProfil;
use App\Services\ProfilRiwayat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        $search = $request->input('search', '');
        $role = $request->input('role', '');
        $status = $request->input('status', '');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%")
                    ->orWhere('prodi', 'like', "%{$search}%");
            });
        }

        if ($role !== '') {
            $query->where('role', $role);
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($request->filled('prodi')) {
            $query->where('prodi', $request->input('prodi'));
        }

        if ($request->filled('semester')) {
            // Semester berjalan dihitung dari angkatan — petakan ke angkatan.
            $angkatan = User::angkatanUntukSemester((int) $request->input('semester'));
            if (is_null($angkatan)) {
                // Tidak ada angkatan yang cocok periode ini (mis. semester
                // genap di periode ganjil) — paksa hasil kosong.
                $query->whereRaw('0 = 1');
            } else {
                $query->where('angkatan', $angkatan);
            }
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role', 'status', 'prodi', 'semester']),
            'rolesList' => Role::orderBy('name')->pluck('name'),
            'prodiList' => User::PRODI,
            'semesterList' => User::SEMESTER_LIST,
            'statusList' => User::STATUS,
            'user' => Auth::user(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Users/Create', [
            'prodiList' => User::PRODI,
            'statusList' => User::STATUS,
            'kelasList' => User::KELAS,
            'rolesList' => Role::orderBy('name')->pluck('name'),
        ]);
    }

    public function store(Request $request)
    {
        $this->normalizePrestasiInput($request);
        $validated = $request->validate($this->rules());

        $validated['password'] = Hash::make($validated['password']);
        $validated['privasi'] = $validated['privasi'] ?? false;
        $validated['prestasi'] = User::normalizePrestasi($validated['prestasi'] ?? null);
        // Foto profil: nullable, max 1 MB (hemat storage). Disimpan di disk public/foto-profil.
        $validated['foto'] = FotoProfil::simpan($request->file('foto'));
        unset($validated['hapus_foto']);

        $user = User::create($validated);
        // Sinkron kolom legacy + Spatie agar dua sistem tetap konsisten.
        $user->assignRole($validated['role']);

        ActivityLog::record(Auth::user(), ActivityLog::CREATE, "Membuat user {$user->name} ({$user->nim}).", $user);

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
    }

    public function show(User $user)
    {
        return Inertia::render('Admin/Users/Show', [
            'user' => $user,
            ...ProfilRiwayat::untuk($user),
        ]);
    }

    public function edit(User $user)
    {
        $user->loadMissing('roles');

        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
            'prodiList' => User::PRODI,
            'statusList' => User::STATUS,
            'kelasList' => User::KELAS,
            'rolesList' => Role::orderBy('name')->pluck('name'),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->normalizePrestasiInput($request);
        $validated = $request->validate($this->rules($user));

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['privasi'] = $validated['privasi'] ?? false;
        $validated['prestasi'] = User::normalizePrestasi($validated['prestasi'] ?? null);

        // Foto: upload baru gantikan lama (file lama dihapus agar hemat),
        // atau hapus bila dicentang hapus_foto.
        if ($request->hasFile('foto')) {
            $validated['foto'] = FotoProfil::simpan($request->file('foto'), $user->foto);
        } elseif ($request->boolean('hapus_foto')) {
            FotoProfil::hapus($user->foto);
            $validated['foto'] = null;
        } else {
            unset($validated['foto']);
        }
        unset($validated['hapus_foto']);

        $user->update($validated);
        $user->syncRoles([$validated['role']]);

        ActivityLog::record(Auth::user(), ActivityLog::UPDATE, "Memperbarui user {$user->name} ({$user->nim}).", $user);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $identitas = "{$user->name} ({$user->nim})";
        $user->delete();

        ActivityLog::record(Auth::user(), ActivityLog::DELETE, "Menghapus user {$identitas}.");

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    /**
     * Ubah input prestasi string lama menjadi array agar lolos validasi array.
     */
    private function normalizePrestasiInput(Request $request): void
    {
        if ($request->has('prestasi') && is_string($request->input('prestasi'))) {
            $normalized = User::normalizePrestasi($request->input('prestasi'));
            $request->merge(['prestasi' => $normalized ? explode("\n", $normalized) : []]);
        }
    }

    private function rules(?User $user = null): array
    {
        $tahunSekarang = (int) now()->year;

        $passwordRule = $user
            ? 'nullable|string|min:6|confirmed'
            : 'required|string|min:6|confirmed';

        return [
            'name' => 'required|string|max:255',
            'nim' => ['required', 'string', Rule::unique('users', 'nim')->ignore($user?->id)],
            'password' => $passwordRule,
            // Role dinamis dari tabel roles — jabatan baru otomatis valid.
            'role' => ['required', Rule::exists('roles', 'name')],
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
            'prodi' => ['required', Rule::in(User::PRODI)],
            'angkatan' => "required|integer|min:2000|max:{$tahunSekarang}",
            'kelas' => ['nullable', Rule::in(User::KELAS)],
            'status' => ['required', Rule::in(User::STATUS)],
            'desa' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date|before_or_equal:today',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'nomor_telepon' => 'nullable|string|max:20',
            'kata_motivasi' => 'nullable|string|max:1000',
            'foto' => FotoProfil::rules(),
            'hapus_foto' => 'nullable|boolean',
            'prestasi' => 'nullable|array|max:50',
            'prestasi.*' => 'nullable|string|max:255',
            'privasi' => 'boolean',
        ];
    }
}
