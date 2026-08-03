<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'total' => Asset::query()->count(),
                'unassigned' => Asset::query()->whereNull('assigned_user_id')->count(),
                'byStatus' => Asset::query()->selectRaw('status, count(*) as count')
                    ->groupBy('status')->pluck('count', 'status'),
                'byCategory' => Asset::query()->selectRaw('category, count(*) as count')
                    ->groupBy('category')->pluck('count', 'category'),
                'totalValue' => Asset::query()->sum('value'),
            ],
        ]);
    }
}
