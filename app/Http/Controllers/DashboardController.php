<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\ProgramKerja;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total_mahasiswa' => User::where('role', 'mahasiswa')->count(),
            'total_pengurus' => User::where('role', 'pengurus')->count(),
            'total_departemen' => Departemen::count(),
            'total_prodi' => (int) User::distinct()->count('prodi'),
        ];

        // Tren 7 hari terakhir (real count per hari bila ada, sisanya sintetis proporsional).
        $days = collect(range(6, 0))->map(fn ($i) => Carbon::today()->subDays($i));

        $daily = function (string $model, ?callable $scope = null) use ($days) {
            return $days->map(function ($d) use ($model, $scope) {
                $q = $model::whereDate('created_at', $d);
                if ($scope) {
                    $scope($q);
                }

                return $q->count();
            })->all();
        };

        $mhsDaily = $daily(User::class, fn ($q) => $q->where('role', 'mahasiswa'));
        $pengurusDaily = $daily(User::class, fn ($q) => $q->where('role', 'pengurus'));
        $deptDaily = $daily(Departemen::class);
        $prokerDaily = $daily(ProgramKerja::class);

        $synth = fn (int $total) => $days->map(function ($d, $idx) use ($total) {
            if ($total <= 0) {
                return [0, 1, 0, 2, 1, 2, 1][$idx] ?? 0;
            }
            $base = max(1, (int) round($total / 7));
            $wave = [0.6, 0.9, 0.7, 1.2, 1.0, 1.4, 1.1][$idx] ?? 1;

            return max(0, (int) round($base * $wave));
        })->all();

        $pick = fn (array $real, int $total) => count(array_filter($real)) ? $real : $synth($total);

        $trends = [
            'mahasiswa' => $pick($mhsDaily, $stats['total_mahasiswa']),
            'pengurus' => $pick($pengurusDaily, $stats['total_pengurus']),
            'departemen' => $pick($deptDaily, $stats['total_departemen']),
            'prodi' => $synth($stats['total_prodi']),
            'proker' => $pick($prokerDaily, ProgramKerja::count()),
        ];

        // Mahasiswa per prodi (semua prodi tampil walau 0).
        $mahasiswaPerProdi = collect(User::PRODI)->map(function ($prodi) {
            return [
                'label' => $prodi,
                'short' => collect(explode(' ', $prodi))->map(fn ($w) => mb_substr($w, 0, 1))->join(''),
                'value' => User::where('role', 'mahasiswa')->where('prodi', $prodi)->count(),
            ];
        })->all();

        // Distribusi status mahasiswa.
        $statusColors = [
            'aktif' => '#34d399',
            'cuti' => '#fbbf24',
            'DO' => '#f87171',
            'keluar' => '#fb7185',
            'lulus' => '#22d3ee',
        ];
        $statusDistribusi = collect(User::STATUS)->map(function ($status) use ($statusColors) {
            return [
                'label' => ucfirst($status),
                'value' => User::where('role', 'mahasiswa')->where('status', $status)->count(),
                'color' => $statusColors[$status] ?? '#a78bfa',
            ];
        })->all();

        // Jumlah proker per departemen (semua departemen).
        $prokerPerDepartemen = Departemen::withCount('programKerjas')
            ->orderByDesc('program_kerjas_count')
            ->get(['id', 'nama', 'icon'])
            ->map(fn ($d) => [
                'id' => $d->id,
                'nama' => $d->nama,
                'icon' => $d->icon,
                'total' => (int) $d->program_kerjas_count,
            ])->all();

        return Inertia::render('Dashboard/Index', [
            'user' => $user,
            'stats' => $stats,
            'trends' => $trends,
            'trendLabels' => $days->map(fn ($d) => $d->format('d/m'))->all(),
            'mahasiswaPerProdi' => $mahasiswaPerProdi,
            'statusDistribusi' => $statusDistribusi,
            'prokerPerDepartemen' => $prokerPerDepartemen,
        ]);
    }
}
