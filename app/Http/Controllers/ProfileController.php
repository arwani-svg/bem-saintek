<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\ProfilRiwayat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /** Lihat identitas diri sendiri (semua info ditampilkan). */
    public function show()
    {
        $me = Auth::user();

        return Inertia::render('Profil/Show', [
            'profile' => $me->fresh(),
            'canEditPrestasi' => $me->isPengurus(),
            'user' => $me,
            ...ProfilRiwayat::untuk($me),
        ]);
    }

    /** Form edit identitas diri sendiri. */
    public function edit()
    {
        $me = Auth::user();

        return Inertia::render('Profil/Edit', [
            'profile' => $me->fresh(),
            'canEditPrestasi' => $me->isPengurus(),
            'user' => $me,
        ]);
    }

    /**
     * Update identitas diri sendiri.
     *
     * Mahasiswa boleh ubah: nama, password, email, nomor telepon,
     * jenis kelamin, dan data pribadi (TTL, alamat, motivasi, privasi).
     * Prestasi HANYA boleh diubah pengurus/admin.
     * NIM, role, prodi, angkatan, kelas, status dikunci (data resmi).
     */
    public function update(Request $request)
    {
        $me = Auth::user();
        $isPengurus = $me->isPengurus();

        $this->normalizePrestasiInput($request);

        $rules = [
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($me->id)],
            'nomor_telepon' => 'nullable|string|max:20',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date|before_or_equal:today',
            'desa' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'kata_motivasi' => 'nullable|string|max:1000',
            'privasi' => 'boolean',
        ];

        if ($isPengurus) {
            $rules['prestasi'] = 'nullable|array|max:50';
            $rules['prestasi.*'] = 'nullable|string|max:255';
        }

        $validated = $request->validate($rules);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Mahasiswa tidak boleh menyelundupkan prestasi / field resmi.
        unset(
            $validated['nim'], $validated['role'], $validated['prodi'],
            $validated['angkatan'], $validated['kelas'], $validated['status']
        );

        if ($isPengurus) {
            $validated['prestasi'] = User::normalizePrestasi($validated['prestasi'] ?? null);
        } else {
            unset($validated['prestasi']);
        }

        $validated['privasi'] = $validated['privasi'] ?? false;

        $me->update($validated);

        ActivityLog::record($me, ActivityLog::UPDATE, 'Memperbarui profil sendiri.');

        return redirect()->route('profil.show')->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Ubah input prestasi string menjadi array agar lolos validasi array.
     */
    private function normalizePrestasiInput(Request $request): void
    {
        if ($request->has('prestasi') && is_string($request->input('prestasi'))) {
            $normalized = User::normalizePrestasi($request->input('prestasi'));
            $request->merge(['prestasi' => $normalized ? explode("\n", $normalized) : []]);
        }
    }
}
