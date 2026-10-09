<?php

namespace App\Http\Middleware;

use App\Models\Departemen;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    public function share($request): array
    {
        return [
            ...parent::share($request),
            // Daftar departemen untuk dropdown navbar & footer (lazy: hanya jalan saat diakses)
            'navDepartemens' => fn () => Departemen::orderBy('nama')
                ->get(['id', 'nama', 'kepanjangan', 'icon', 'deskripsi']),
            // Hak akses user login (lazy): dipakai frontend untuk
            // menampilkan/menyembunyikan menu per role/permission.
            'auth' => fn () => $request->user() ? [
                'user' => $request->user()->only(['id', 'name', 'nim', 'role', 'prodi']),
                'roles' => $request->user()->getRoleNames()->toArray(),
                'permissions' => $request->user()->getAllPermissions()->pluck('name')->toArray(),
            ] : null,
        ];
    }
}
