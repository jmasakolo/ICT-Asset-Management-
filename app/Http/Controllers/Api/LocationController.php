<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\JsonResponse;

/**
 * Minimal location directory for the mobile app's asset intake form —
 * id/name only, ordered by name.
 */
class LocationController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Location::orderBy('name')->get(['id', 'name']),
        ]);
    }
}
