<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaintenanceRecordRequest;
use App\Models\Asset;
use App\Models\MaintenanceRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    public function index(): View
    {
        return view('admin.maintenance.index', [
            'records' => MaintenanceRecord::with('asset')->orderBy('performed_at', 'desc')->get(),
            'assets' => Asset::orderBy('name')->get(),
        ]);
    }

    public function store(MaintenanceRecordRequest $request): RedirectResponse
    {
        MaintenanceRecord::create($request->validated());

        return redirect()
            ->route('admin.maintenance.index')
            ->with('status', 'Maintenance record added.');
    }
}
