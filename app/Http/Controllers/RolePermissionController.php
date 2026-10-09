<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    /** Role sistem yang tidak boleh dihapus/diubah namanya. */
    public const PROTECTED_ROLES = ['mahasiswa', 'pengurus'];

    public function index()
    {
        $roles = Role::with('permissions')
            ->orderBy('name')
            ->get()
            ->map(fn (Role $role) => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name')->toArray(),
                'is_protected' => in_array($role->name, self::PROTECTED_ROLES, true),
            ]);

        return Inertia::render('Admin/Roles/Index', [
            'roles' => $roles,
            'user' => Auth::user(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Roles/Create', [
            'allPermissions' => Permission::orderBy('name')->get(['id', 'name']),
            'user' => Auth::user(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'regex:/^[a-z0-9-]+$/', Rule::unique('roles', 'name')],
            'permissions' => 'nullable|array',
            'permissions.*' => [Rule::exists('permissions', 'name')],
        ], [
            'name.regex' => 'Nama role hanya boleh huruf kecil, angka, dan strip (contoh: ketua-bem).',
        ]);

        $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);
        $role->syncPermissions($validated['permissions'] ?? []);

        ActivityLog::record(Auth::user(), ActivityLog::CREATE, "Membuat role {$role->name}.");

        return redirect()->route('roles.index')->with('success', "Role {$role->name} berhasil dibuat.");
    }

    public function edit(Role $role)
    {
        $role->load('permissions');

        return Inertia::render('Admin/Roles/Edit', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name')->toArray(),
                'is_protected' => in_array($role->name, self::PROTECTED_ROLES, true),
            ],
            'allPermissions' => Permission::orderBy('name')->get(['id', 'name']),
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:50', 'regex:/^[a-z0-9-]+$/',
                Rule::unique('roles', 'name')->ignore($role->id),
            ],
            'permissions' => 'nullable|array',
            'permissions.*' => [Rule::exists('permissions', 'name')],
        ], [
            'name.regex' => 'Nama role hanya boleh huruf kecil, angka, dan strip (contoh: ketua-bem).',
        ]);

        // Role sistem boleh diubah permission-nya, tapi tidak boleh ganti nama.
        if (in_array($role->name, self::PROTECTED_ROLES, true)) {
            $validated['name'] = $role->name;
        }

        $oldName = $role->name;
        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);

        // Jaga kolom legacy tetap sinkron bila role diganti nama.
        if ($oldName !== $role->name) {
            User::where('role', $oldName)->update(['role' => $role->name]);
        }

        ActivityLog::record(Auth::user(), ActivityLog::UPDATE, "Memperbarui role {$role->name}.");

        return redirect()->route('roles.index')->with('success', "Role {$role->name} berhasil diperbarui.");
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, self::PROTECTED_ROLES, true)) {
            return back()->with('error', "Role {$role->name} adalah role sistem dan tidak bisa dihapus.");
        }

        // User yang kehilangan role dikembalikan ke mahasiswa agar tidak yatim.
        $affected = User::role($role->name)->get();
        foreach ($affected as $user) {
            $user->syncRoles(['mahasiswa']);
            $user->update(['role' => 'mahasiswa']);
        }

        $role->delete();

        ActivityLog::record(Auth::user(), ActivityLog::DELETE, "Menghapus role {$role->name}.");

        return redirect()->route('roles.index')
            ->with('success', "Role {$role->name} dihapus. {$affected->count()} user dikembalikan ke mahasiswa.");
    }
}
