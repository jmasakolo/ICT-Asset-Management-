<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssetRequest;
use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function index(Request $request): View
    {
        $query = Asset::with(['assignedUser', 'department', 'location'])->orderBy('id', 'desc');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('category', 'ilike', "%{$search}%")
                    ->orWhere('serial_number', 'ilike', "%{$search}%")
                    ->orWhere('asset_tag', 'ilike', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            if (in_array($status, Asset::STATUSES, true)) {
                $query->where('status', $status);
            }
        }

        return view('admin.assets.index', [
            'assets' => $query->get(),
            'search' => $search ?? '',
            'status' => $status ?? '',
            'editing' => $request->filled('edit') ? Asset::find($request->integer('edit')) : null,
            'users' => User::orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
        ]);
    }

    public function store(AssetRequest $request): RedirectResponse
    {
        $asset = Asset::create($request->validated());

        AuditLog::record('created', $asset, "Created asset “{$asset->name}”.");

        return redirect()
            ->route('admin.assets.index')
            ->with('status', "Added “{$asset->name}”.");
    }

    public function update(AssetRequest $request, Asset $asset): RedirectResponse
    {
        $asset->update($request->validated());

        AuditLog::record('updated', $asset, "Updated asset “{$asset->name}”.");

        return redirect()
            ->route('admin.assets.index')
            ->with('status', "Updated “{$asset->name}”.");
    }

    public function destroy(Asset $asset): RedirectResponse
    {
        $name = $asset->name;
        $asset->delete();

        AuditLog::record('deleted', null, "Deleted asset “{$name}”.");

        return redirect()
            ->route('admin.assets.index')
            ->with('status', "Deleted “{$name}”.");
    }
}
