<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    /**
     * Halaman log aktivitas — 25 baris per halaman, hanya yang penting
     * (login/logout, create, update, delete, pendaftaran, status).
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user:id,name,nim')->latest('id');

        if ($search = trim((string) $request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('activity', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('nim', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('activity')) {
            $query->where('activity', $request->input('activity'));
        }

        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->input('dari'));
        }

        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->input('sampai'));
        }

        return Inertia::render('Admin/ActivityLog/Index', [
            'logs' => $query->paginate(25)->withQueryString(),
            'filters' => $request->only(['search', 'activity', 'dari', 'sampai']),
            'activities' => ActivityLog::ACTIVITIES,
            'user' => Auth::user(),
        ]);
    }
}
