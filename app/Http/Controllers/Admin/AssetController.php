<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssetRequest;
use App\Models\Asset;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function index(): View
    {
        return view('admin.assets.index', [
            'assets' => Asset::with('assignedUser')->orderBy('id', 'desc')->get(),
        ]);
    }

    public function store(AssetRequest $request): RedirectResponse
    {
        $asset = Asset::create($request->validated());

        return redirect()
            ->route('admin.assets.index')
            ->with('status', "Added “{$asset->name}”.");
    }
}
