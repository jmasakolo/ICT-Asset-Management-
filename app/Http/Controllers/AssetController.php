<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssetRequest;
use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function index(Request $request): View
    {
        $editing = null;

        if ($request->filled('edit')) {
            // Scoped the same as the list itself — a user can only ever
            // land in edit mode on an asset actually assigned to them.
            $editing = Asset::where('assigned_user_id', Auth::id())->find($request->integer('edit'));
        }

        return view('assets.index', [
            'assets' => Asset::where('assigned_user_id', Auth::id())
                ->orderBy('id', 'desc')
                ->get(),
            'editing' => $editing,
            'departments' => Department::orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
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

    public function update(AssetRequest $request, Asset $asset): RedirectResponse
    {
        abort_if($asset->assigned_user_id !== Auth::id(), 403);

        $asset->update([
            ...$request->validated(),
            // A user can edit their own asset's details but can't hand it
            // off to someone else from this self-service form.
            'assigned_user_id' => Auth::id(),
        ]);

        AuditLog::record('updated', $asset, "Updated asset “{$asset->name}”.");

        return redirect()
            ->route('assets.index')
            ->with('status', "Updated “{$asset->name}”.");
    }

    public function destroy(Asset $asset): RedirectResponse
    {
        abort_if($asset->assigned_user_id !== Auth::id(), 403);

        $name = $asset->name;
        $asset->delete();

        AuditLog::record('deleted', null, "Deleted asset “{$name}”.");

        return redirect()
            ->route('assets.index')
            ->with('status', "Deleted “{$name}”.");
    }
}
