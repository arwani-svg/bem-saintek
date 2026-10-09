<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\GelombangPendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class GelombangPendaftaranController extends Controller
{
    public function create()
    {
        return Inertia::render('Admin/Gelombang/Create', [
            'user' => Auth::user(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());
        $validated['is_open'] = (bool) ($validated['is_open'] ?? false);

        // Satu gelombang dibuka dalam satu waktu — tutup yang lain.
        if ($validated['is_open']) {
            GelombangPendaftaran::where('is_open', true)->update(['is_open' => false]);
        }

        $gelombang = GelombangPendaftaran::create($validated);

        ActivityLog::record(Auth::user(), ActivityLog::CREATE, "Membuat periode pendaftaran {$gelombang->nama}.", $gelombang);

        return redirect()->route('pendaftaran.admin.index', ['tab' => 'periode'])->with('success', "Periode {$gelombang->nama} berhasil dibuat.");
    }

    public function edit(GelombangPendaftaran $gelombang)
    {
        return Inertia::render('Admin/Gelombang/Edit', [
            'gelombang' => $this->format($gelombang),
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request, GelombangPendaftaran $gelombang)
    {
        $validated = $request->validate($this->rules());
        $validated['is_open'] = (bool) ($validated['is_open'] ?? false);

        if ($validated['is_open']) {
            GelombangPendaftaran::where('id', '!=', $gelombang->id)->where('is_open', true)->update(['is_open' => false]);
        }

        $gelombang->update($validated);

        ActivityLog::record(Auth::user(), ActivityLog::UPDATE, "Memperbarui periode pendaftaran {$gelombang->nama}.", $gelombang);

        return redirect()->route('pendaftaran.admin.index', ['tab' => 'periode'])->with('success', "Periode {$gelombang->nama} berhasil diperbarui.");
    }

    public function destroy(GelombangPendaftaran $gelombang)
    {
        $nama = $gelombang->nama;
        $gelombang->delete();

        return redirect()->route('pendaftaran.admin.index', ['tab' => 'periode'])->with('success', "Periode {$nama} berhasil dihapus.");
    }

    /** Buka / tutup cepat dari dashboard. */
    public function toggle(GelombangPendaftaran $gelombang)
    {
        if (! $gelombang->is_open) {
            GelombangPendaftaran::where('id', '!=', $gelombang->id)->where('is_open', true)->update(['is_open' => false]);
        }

        $gelombang->update(['is_open' => ! $gelombang->is_open]);

        $aksi = $gelombang->is_open ? 'ditutup' : 'dibuka';

        ActivityLog::record(Auth::user(), ActivityLog::STATUS, "Periode pendaftaran {$gelombang->nama} {$aksi}.", $gelombang);

        return back()->with('success', "Periode {$gelombang->nama} berhasil {$aksi}.");
    }

    private function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'is_open' => 'boolean',
        ];
    }

    private function format(GelombangPendaftaran $g): array
    {
        return [
            'id' => $g->id,
            'nama' => $g->nama,
            'deskripsi' => $g->deskripsi,
            'tanggal_mulai' => $g->tanggal_mulai?->format('Y-m-d'),
            'tanggal_selesai' => $g->tanggal_selesai?->format('Y-m-d'),
            'is_open' => (bool) $g->is_open,
            'sedang_dibuka' => $g->sedangDibuka(),
            'status_label' => $g->statusLabel(),
            'total_pendaftar' => $g->pendaftarans_count ?? $g->pendaftarans()->count(),
        ];
    }
}
