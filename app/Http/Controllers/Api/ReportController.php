<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

/**
 * Lets the mobile app trigger the same PDF the admin dashboard downloads,
 * reusing AdminReportController::data() so the two surfaces can't drift.
 */
class ReportController extends Controller
{
    public function pdf(AdminReportController $adminReports): Response
    {
        return Pdf::loadView('admin.reports.pdf', $adminReports->data())
            ->download('report.pdf');
    }
}
