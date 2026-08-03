<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\MaintenanceRecord;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('admin.reports.index', $this->data());
    }

    public function pdf(): Response
    {
        return Pdf::loadView('admin.reports.pdf', $this->data())
            ->download('report.pdf');
    }

    /**
     * @return array<string, mixed>
     */
    private function data(): array
    {
        $maintenanceCount = MaintenanceRecord::count();
        $maintenanceTotalCost = MaintenanceRecord::sum('cost');

        return [
            'maintenance' => [
                'count' => $maintenanceCount,
                'totalCost' => $maintenanceTotalCost,
                'averageCost' => $maintenanceCount > 0 ? $maintenanceTotalCost / $maintenanceCount : 0,
            ],
            'users' => [
                'total' => User::count(),
                'withAssets' => User::has('assets')->count(),
                'withoutAssets' => User::doesntHave('assets')->count(),
            ],
            'topAssetsByCost' => Asset::query()
                ->withCount('maintenanceRecords')
                ->withSum('maintenanceRecords', 'cost')
                ->get()
                ->filter(fn (Asset $asset) => $asset->maintenance_records_count > 0)
                ->sortByDesc('maintenance_records_sum_cost')
                ->take(10),
        ];
    }
}
