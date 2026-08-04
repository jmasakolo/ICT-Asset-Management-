<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::orderBy('created_at', 'desc');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('actor_label', 'ilike', "%{$search}%")
                    ->orWhere('action', 'ilike', "%{$search}%")
                    ->orWhere('subject_type', 'ilike', "%{$search}%")
                    ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        return view('admin.audit-logs.index', [
            'logs' => $query->paginate(25)->withQueryString(),
            'search' => $search ?? '',
        ]);
    }
}
