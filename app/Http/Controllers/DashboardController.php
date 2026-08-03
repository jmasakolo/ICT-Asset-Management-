<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $assets = Asset::query()->where('assigned_user_id', Auth::id());

        return view('dashboard', [
            'stats' => [
                'total' => (clone $assets)->count(),
                'byStatus' => (clone $assets)->selectRaw('status, count(*) as count')
                    ->groupBy('status')->pluck('count', 'status'),
                'byCategory' => (clone $assets)->selectRaw('category, count(*) as count')
                    ->groupBy('category')->pluck('count', 'category'),
                'totalValue' => (clone $assets)->sum('value'),
            ],
        ]);
    }
}
