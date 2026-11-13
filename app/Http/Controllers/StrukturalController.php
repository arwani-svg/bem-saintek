<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Departemen;
use App\Models\Struktural;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class StrukturalController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $periode = $request->input('periode', '');
        $departemenId = $request->input('departemen_id', '');

        $strukturals = Struktural::with(['user', 'departemen'])
            ->when($search !== '', fn ($q) => $q->where(function ($w) use ($search) {
                $w->where('jabatan', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")
                        ->orWhere('nim', 'like', "%{$search}%")
                        ->orWhere('prodi', 'like', "%{$search}%"));
            }))
            ->when($periode !== '', fn ($q) => $q->where('periode', $periode))
            ->when($departemenId === '__ph__', fn ($q) => $q->whereNull('departemen_id'))
            ->when($departemenId !== '' && $departemenId !== '__ph__', fn ($q) => $q->where('departemen_id', $departemenId))
            ->orderByDesc('periode')
            ->orderBy('urutan')
            ->orderBy('jabatan')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Struktural/Index', [
            'strukturals' => $strukturals,
            'filters' => $request->only(['search', 'periode', 'departemen_id']),
            'periods' => $this->periods(),
            'departemens' => Departemen::orderBy('nama')->get(['id', 'nama', 'icon']),
            'users' => $this->users(),
            'user' => Auth::user(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        $struktural = Struktural::create($validated);

        ActivityLog::record(
            Auth::user(),
            ActivityLog::CREATE,
            "Menambah struktural {$struktural->jabatan} periode {$struktural->periode}.",
            $struktural,
        );

        return redirect()->route('struktural.index')->with('success', 'Data struktural berhasil dibuat.');
    }

    public function update(Request $request, Struktural $struktural)
    {
        $validated = $this->validateData($request, $struktural);

        $struktural->update($validated);

        ActivityLog::record(
            Auth::user(),
            ActivityLog::UPDATE,
            "Memperbarui struktural {$struktural->jabatan} periode {$struktural->periode}.",
            $struktural,
        );

        return redirect()->route('struktural.index')->with('success', 'Data struktural berhasil diperbarui.');
    }

    public function destroy(Struktural $struktural)
    {
        $identitas = "{$struktural->jabatan} periode {$struktural->periode}";

        $struktural->delete();

        ActivityLog::record(Auth::user(), ActivityLog::DELETE, "Menghapus struktural {$identitas}.");

        return redirect()->route('struktural.index')->with('success', 'Data struktural berhasil dihapus.');
    }

    private function validateData(Request $request, ?Struktural $struktural = null): array
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'departemen_id' => ['nullable', 'integer', Rule::exists('departemens', 'id')],
            'jabatan' => ['required', 'string', 'max:100'],
            'periode' => ['required', 'string', 'max:20'],
            'urutan' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ]);

        $validated['departemen_id'] = $validated['departemen_id'] ?: null;
        $validated['urutan'] = $validated['urutan'] ?? 0;

        $duplicate = Struktural::where('user_id', $validated['user_id'])
            ->where('periode', $validated['periode'])
            ->where('jabatan', $validated['jabatan'])
            ->when($struktural, fn ($q) => $q->whereKeyNot($struktural->getKey()))
            ->exists();

        if ($duplicate) {
            throw ValidationException::withMessages([
                'jabatan' => 'User ini sudah memiliki jabatan yang sama pada periode tersebut.',
            ]);
        }

        return $validated;
    }

    private function periods()
    {
        return Struktural::query()
            ->select('periode')
            ->distinct()
            ->orderByDesc('periode')
            ->pluck('periode');
    }

    private function users()
    {
        return User::orderBy('name')
            ->get(['id', 'name', 'nim', 'prodi', 'role', 'foto']);
    }
}
