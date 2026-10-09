<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Informasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class InformasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Informasi::query();

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

        $informasis = $query->latest('tanggal_publikasi')->latest()->paginate(10)->withQueryString();

        // Tambahkan URL gambar publik untuk tiap item
        $informasis->getCollection()->transform(fn (Informasi $i) => $this->formatAdmin($i));

        return Inertia::render('Admin/Informasi/Index', [
            'informasis' => $informasis,
            'filters' => $request->only(['search', 'jenis']),
            'jenisList' => Informasi::JENIS,
            'user' => Auth::user(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Informasi/Create', [
            'jenisList' => Informasi::JENIS,
            'jenisArtikel' => Informasi::JENIS_ARTIKEL,
            'user' => Auth::user(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        $validated['slug'] = $this->uniqueSlug($validated['judul']);
        $validated['tanggal_publikasi'] = $validated['tanggal_publikasi'] ?? now()->toDateString();
        unset($validated['_hapus_gambar']);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('informasi', 'public');
        }

        Informasi::create($validated);

        ActivityLog::record(Auth::user(), ActivityLog::CREATE, "Membuat informasi {$validated['judul']}.");

        return redirect()->route('informasi.index')->with('success', 'Informasi berhasil dibuat.');
    }

    public function edit(Informasi $informasi)
    {
        return Inertia::render('Admin/Informasi/Edit', [
            'informasi' => $this->formatAdmin($informasi),
            'jenisList' => Informasi::JENIS,
            'jenisArtikel' => Informasi::JENIS_ARTIKEL,
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request, Informasi $informasi)
    {
        $validated = $this->validateData($request, $informasi);

        // Slug ikut judul baru, tapi jaga keunikan (kecuali milik sendiri)
        if ($validated['judul'] !== $informasi->judul) {
            $validated['slug'] = $this->uniqueSlug($validated['judul'], $informasi->id);
        }

        $hapusGambar = $validated['_hapus_gambar'] ?? false;
        unset($validated['_hapus_gambar']);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama agar storage tidak menumpuk
            if ($informasi->gambar && Storage::disk('public')->exists($informasi->gambar)) {
                Storage::disk('public')->delete($informasi->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('informasi', 'public');
        } elseif ($hapusGambar) {
            if ($informasi->gambar && Storage::disk('public')->exists($informasi->gambar)) {
                Storage::disk('public')->delete($informasi->gambar);
            }
            $validated['gambar'] = null;
        }

        // Checkbox yang tidak dicentang tidak terkirim -> default false
        $validated['is_published'] = $validated['is_published'] ?? false;
        $validated['is_unggulan'] = $validated['is_unggulan'] ?? false;

        // Kalau ganti ke jenis gambar-only, konten lama dibersihkan opsional — biarkan, tapi pastikan ringkasan ada
        $informasi->update($validated);

        ActivityLog::record(Auth::user(), ActivityLog::UPDATE, "Memperbarui informasi {$informasi->judul}.", $informasi);

        return redirect()->route('informasi.index')->with('success', 'Informasi berhasil diperbarui.');
    }

    public function destroy(Informasi $informasi)
    {
        if ($informasi->gambar && Storage::disk('public')->exists($informasi->gambar)) {
            Storage::disk('public')->delete($informasi->gambar);
        }
        $judul = $informasi->judul;
        $informasi->delete();

        ActivityLog::record(Auth::user(), ActivityLog::DELETE, "Menghapus informasi {$judul}.");

        return redirect()->route('informasi.index')->with('success', 'Informasi berhasil dihapus.');
    }

    private function validateData(Request $request, ?Informasi $ignore = null): array
    {
        $jenisValues = implode(',', array_keys(Informasi::JENIS));

        $validated = $request->validate([
            'jenis' => "required|in:{$jenisValues}",
            'judul' => 'required|string|max:255',
            'ringkasan' => 'nullable|string|max:1000',
            'konten' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'tanggal_publikasi' => 'nullable|date',
            'is_published' => 'boolean',
            'is_unggulan' => 'boolean',
            'hapus_gambar' => 'boolean',
        ]);

        // Aturan fleksibel per jenis:
        // - berita & penghargaan: wajib ada konten (narasi panjang)
        // - infografis & lomba: wajib ada ringkasan (caption singkat) + gambar
        $jenis = $validated['jenis'];
        $gambarAda = $request->hasFile('gambar') || ($ignore?->gambar && empty($validated['hapus_gambar']));

        if (in_array($jenis, Informasi::JENIS_ARTIKEL, true) && empty($validated['konten'])) {
            throw ValidationException::withMessages([
                'konten' => 'Narasi / konten wajib diisi untuk ' . Informasi::JENIS[$jenis] . '.',
            ]);
        }

        if (in_array($jenis, Informasi::JENIS_GAMBAR, true)) {
            if (empty($validated['ringkasan'])) {
                throw ValidationException::withMessages([
                    'ringkasan' => 'Keterangan singkat wajib diisi untuk ' . Informasi::JENIS[$jenis] . '.',
                ]);
            }
            if (! $gambarAda) {
                throw ValidationException::withMessages([
                    'gambar' => 'Gambar wajib diunggah untuk ' . Informasi::JENIS[$jenis] . '.',
                ]);
            }
        }

        // Hapus gambar atas permintaan user (edit)
        $hapusGambar = ! empty($validated['hapus_gambar']);
        unset($validated['hapus_gambar']);
        unset($validated['gambar']); // file fisik ditangani pemanggil via store()

        // Normalisasi boolean dari form Inertia (bisa string "1"/"0")
        $validated['is_published'] = filter_var($validated['is_published'] ?? ($ignore?->is_published ?? true), FILTER_VALIDATE_BOOLEAN);
        $validated['is_unggulan'] = filter_var($validated['is_unggulan'] ?? false, FILTER_VALIDATE_BOOLEAN);

        // Teruskan flag hapus gambar ke pemanggil
        $validated['_hapus_gambar'] = $hapusGambar;

        return $validated;
    }

    private function uniqueSlug(string $judul, ?int $ignoreId = null): string
    {
        $base = Str::slug($judul) ?: 'informasi';
        $slug = $base;
        $i = 2;
        while (Informasi::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }
        return $slug;
    }

    private function formatAdmin(Informasi $i): array
    {
        return [
            'id' => $i->id,
            'jenis' => $i->jenis,
            'jenis_label' => $i->jenis_label,
            'judul' => $i->judul,
            'slug' => $i->slug,
            'ringkasan' => $i->ringkasan,
            'konten' => $i->konten,
            'gambar' => $i->gambar,
            'gambar_url' => $i->gambar_url,
            'tanggal_publikasi' => $i->tanggal_publikasi?->format('Y-m-d'),
            'is_published' => $i->is_published,
            'is_unggulan' => $i->is_unggulan,
            'created_at' => $i->created_at?->format('d M Y'),
        ];
    }
}
