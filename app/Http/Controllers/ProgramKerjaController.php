<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Departemen;
use App\Models\ProgramKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProgramKerjaController extends Controller
{
    public function index(Request $request)
    {
        $query = ProgramKerja::with('departemen');

        $search = $request->input('search', '');
        $departemenId = $request->input('departemen_id', '');
        $status = $request->input('status', '');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('kategori', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        if ($departemenId !== '') {
            $query->where('departemen_id', $departemenId);
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        $prokers = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Admin/Proker/Index', [
            'prokers' => $prokers,
            'filters' => $request->only(['search', 'departemen_id', 'status']),
            'departemens' => Departemen::orderBy('nama')->get(['id', 'nama']),
            'statusList' => ProgramKerja::STATUS,
            'user' => Auth::user(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Proker/Create', [
            'departemens' => Departemen::orderBy('nama')->get(['id', 'nama', 'kepanjangan']),
            'statusList' => ProgramKerja::STATUS,
            'warnaList' => ProgramKerja::WARNA,
            'user' => Auth::user(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'departemen_id' => 'required|exists:departemens,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori' => 'nullable|string|max:100',
            'warna' => 'required|in:' . implode(',', ProgramKerja::WARNA),
            'status' => 'required|in:' . implode(',', array_keys(ProgramKerja::STATUS)),
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'lokasi' => 'nullable|string|max:255',
            'penanggung_jawab' => 'nullable|string|max:255',
            'is_unggulan' => 'boolean',
        ]);

        $validated['is_unggulan'] = $validated['is_unggulan'] ?? false;

        ProgramKerja::create($validated);

        ActivityLog::record(Auth::user(), ActivityLog::CREATE, "Membuat program kerja {$validated['judul']}.");

        return redirect()->route('proker.index')->with('success', 'Program kerja berhasil dibuat.');
    }

    public function edit(ProgramKerja $proker)
    {
        return Inertia::render('Admin/Proker/Edit', [
            'proker' => $proker,
            'departemens' => Departemen::orderBy('nama')->get(['id', 'nama', 'kepanjangan']),
            'statusList' => ProgramKerja::STATUS,
            'warnaList' => ProgramKerja::WARNA,
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request, ProgramKerja $proker)
    {
        $validated = $request->validate([
            'departemen_id' => 'required|exists:departemens,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori' => 'nullable|string|max:100',
            'warna' => 'required|in:' . implode(',', ProgramKerja::WARNA),
            'status' => 'required|in:' . implode(',', array_keys(ProgramKerja::STATUS)),
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'lokasi' => 'nullable|string|max:255',
            'penanggung_jawab' => 'nullable|string|max:255',
            'is_unggulan' => 'boolean',
        ]);

        $validated['is_unggulan'] = $validated['is_unggulan'] ?? false;

        $proker->update($validated);

        ActivityLog::record(Auth::user(), ActivityLog::UPDATE, "Memperbarui program kerja {$proker->judul}.", $proker);

        return redirect()->route('proker.index')->with('success', 'Program kerja berhasil diperbarui.');
    }

    public function destroy(ProgramKerja $proker)
    {
        $judul = $proker->judul;
        $proker->delete();

        ActivityLog::record(Auth::user(), ActivityLog::DELETE, "Menghapus program kerja {$judul}.");

        return redirect()->route('proker.index')->with('success', 'Program kerja berhasil dihapus.');
    }
}
