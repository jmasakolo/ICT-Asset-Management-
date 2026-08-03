<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssetRequest;
use App\Models\Asset;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function index(): View
    {
        return view('assets.index', [
            'assets' => Asset::where('assigned_user_id', Auth::id())
                ->orderBy('id', 'desc')
                ->get(),
        ]);
    }

    public function store(AssetRequest $request): RedirectResponse
    {
        $asset = Asset::create([
            ...$request->validated(),
            'assigned_user_id' => Auth::id(),
        ]);

        AuditLog::record('created', $asset, "Created asset “{$asset->name}” (self-assigned).");

        return redirect()
            ->route('assets.index')
            ->with('status', "Added “{$asset->name}”.");
    }
}
