<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DepartemenController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $departemens = Departemen::withCount('programKerjas')
            ->when($search !== '', fn ($q) => $q->where(function ($w) use ($search) {
                $w->where('nama', 'like', "%{$search}%")
                    ->orWhere('kepanjangan', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Departemen/Index', [
            'departemens' => $departemens,
            'filters' => $request->only(['search']),
            'user' => Auth::user(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Departemen/Create', [
            'user' => Auth::user(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50|unique:departemens,nama',
            'kepanjangan' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'tujuan' => 'nullable|string',
            'icon' => 'nullable|string|max:10',
        ]);

        $validated['icon'] = $validated['icon'] ?: '🏛️';

        Departemen::create($validated);

        ActivityLog::record(Auth::user(), ActivityLog::CREATE, "Membuat departemen {$validated['nama']}.");

        return redirect()->route('departemen.index')->with('success', 'Departemen berhasil dibuat.');
    }

    public function edit(Departemen $departemen)
    {
        return Inertia::render('Admin/Departemen/Edit', [
            'departemen' => $departemen,
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request, Departemen $departemen)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50|unique:departemens,nama,' . $departemen->id,
            'kepanjangan' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'tujuan' => 'nullable|string',
            'icon' => 'nullable|string|max:10',
        ]);

        $validated['icon'] = $validated['icon'] ?: '🏛️';

        $departemen->update($validated);

        ActivityLog::record(Auth::user(), ActivityLog::UPDATE, "Memperbarui departemen {$departemen->nama}.", $departemen);

        return redirect()->route('departemen.index')->with('success', 'Departemen berhasil diperbarui.');
    }

    public function destroy(Departemen $departemen)
    {
        if ($departemen->programKerjas()->exists()) {
            return redirect()->route('departemen.index')
                ->with('error', "Departemen {$departemen->nama} masih memiliki program kerja. Hapus/pindahkan prokernya dulu.");
        }

        $nama = $departemen->nama;
        $departemen->delete();

        ActivityLog::record(Auth::user(), ActivityLog::DELETE, "Menghapus departemen {$nama}.");

        return redirect()->route('departemen.index')->with('success', 'Departemen berhasil dihapus.');
    }
}
