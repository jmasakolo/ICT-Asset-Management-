<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\JsonResponse;

/**
 * Minimal department directory for the mobile app's asset intake form —
 * id/name only, ordered by name.
 */
class DepartmentController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Department::orderBy('name')->get(['id', 'name']),
        ]);
    }
}
