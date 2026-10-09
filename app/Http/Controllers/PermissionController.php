<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::with('roles:id,name')
            ->orderBy('name')
            ->get()
            ->map(fn (Permission $permission) => [
                'id' => $permission->id,
                'name' => $permission->name,
                'description' => $permission->description,
                'roles_count' => $permission->roles->count(),
            ]);

        return Inertia::render('Admin/Permissions/Index', [
            'permissions' => $permissions,
            'user' => Auth::user(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Permissions/Create', [
            'user' => Auth::user(),
        ]);
    }

    /** Tambah permission baru (mis. untuk fitur absensi/arsip/bendahara). */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'regex:/^[a-z0-9-]+$/', Rule::unique('permissions', 'name')],
            'description' => 'nullable|string|max:255',
        ], [
            'name.regex' => 'Nama permission hanya boleh huruf kecil, angka, dan strip (contoh: kelola-rapat).',
        ]);

        Permission::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'guard_name' => 'web',
        ]);

        ActivityLog::record(Auth::user(), ActivityLog::CREATE, "Membuat permission {$validated['name']}.");

        return redirect()->route('permissions.index')->with('success', "Permission {$validated['name']} berhasil dibuat.");
    }

    public function edit(Permission $permission)
    {
        return Inertia::render('Admin/Permissions/Edit', [
            'permission' => $permission->only(['id', 'name', 'description']),
            'user' => Auth::user(),
        ]);
    }

    /** Nama dikunci (dipakai middleware); yang bisa diubah hanya deskripsi. */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'description' => 'nullable|string|max:255',
        ]);

        $permission->update($validated);

        ActivityLog::record(Auth::user(), ActivityLog::UPDATE, "Memperbarui permission {$permission->name}.");

        return redirect()->route('permissions.index')->with('success', "Permission {$permission->name} berhasil diperbarui.");
    }

    public function destroy(Permission $permission)
    {
        if ($permission->roles()->exists()) {
            return back()->with('error', "Permission {$permission->name} masih dipakai role — lepas dulu dari halaman Atur Role.");
        }

        $permission->delete();

        ActivityLog::record(Auth::user(), ActivityLog::DELETE, "Menghapus permission {$permission->name}.");

        return redirect()->route('permissions.index')->with('success', "Permission {$permission->name} dihapus.");
    }
}
