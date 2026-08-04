<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

/**
 * Minimal user directory for the mobile app's assignee picker — id/name/email
 * only, never the fields User::casts()/Hidden already keep off the wire.
 */
class UserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => User::orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }
}
