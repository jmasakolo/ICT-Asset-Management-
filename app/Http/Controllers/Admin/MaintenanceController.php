<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaintenanceRecordRequest;
use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\MaintenanceRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    public function index(Request $request): View
    {
        $query = MaintenanceRecord::with('asset')->orderBy('performed_at', 'desc');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'ilike', "%{$search}%")
                    ->orWhereHas('asset', fn ($aq) => $aq->where('name', 'ilike', "%{$search}%"));
            });
        }

        return view('admin.maintenance.index', [
            'records' => $query->get(),
            'search' => $search ?? '',
            'assets' => Asset::orderBy('name')->get(),
            'editing' => $request->filled('edit') ? MaintenanceRecord::find($request->integer('edit')) : null,
        ]);
    }

    public function store(MaintenanceRecordRequest $request): RedirectResponse
    {
        $record = MaintenanceRecord::create($request->validated());

        AuditLog::record('created', $record, "Logged maintenance for “{$record->asset->name}”: {$record->description}.");

        return redirect()
            ->route('admin.maintenance.index')
            ->with('status', 'Maintenance record added.');
    }

    public function update(MaintenanceRecordRequest $request, MaintenanceRecord $maintenance): RedirectResponse
    {
        $maintenance->update($request->validated());

        AuditLog::record('updated', $maintenance, "Updated maintenance record for “{$maintenance->asset->name}”.");

        return redirect()
            ->route('admin.maintenance.index')
            ->with('status', 'Maintenance record updated.');
    }

    public function destroy(MaintenanceRecord $maintenance): RedirectResponse
    {
        $assetName = $maintenance->asset->name;
        $maintenance->delete();

        AuditLog::record('deleted', null, "Deleted maintenance record for “{$assetName}”.");

        return redirect()
            ->route('admin.maintenance.index')
            ->with('status', 'Maintenance record deleted.');
    }
}
