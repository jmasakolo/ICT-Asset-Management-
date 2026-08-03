<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LocationRequest;
use App\Models\AuditLog;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(): View
    {
        return view('admin.locations.index', [
            'locations' => Location::orderBy('name')->get(),
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
}
