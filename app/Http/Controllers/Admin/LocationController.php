<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LocationRequest;
use App\Models\AuditLog;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.locations.index', [
            'locations' => Location::orderBy('name')->get(),
            'editing' => $request->filled('edit') ? Location::find($request->integer('edit')) : null,
        ]);
    }

    public function store(LocationRequest $request): RedirectResponse
    {
        $location = Location::create($request->validated());

        AuditLog::record('created', $location, "Created location “{$location->name}”.");

        return redirect()
            ->route('admin.locations.index')
            ->with('status', "Added “{$location->name}”.");
    }

    public function update(LocationRequest $request, Location $location): RedirectResponse
    {
        $location->update($request->validated());

        AuditLog::record('updated', $location, "Updated location “{$location->name}”.");

        return redirect()
            ->route('admin.locations.index')
            ->with('status', "Updated “{$location->name}”.");
    }

    public function destroy(Location $location): RedirectResponse
    {
        $name = $location->name;
        $location->delete();

        AuditLog::record('deleted', null, "Deleted location “{$name}”.");

        return redirect()
            ->route('admin.locations.index')
            ->with('status', "Deleted “{$name}”.");
    }
}
