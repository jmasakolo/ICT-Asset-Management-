<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AssetIntakeRequest;
use App\Http\Resources\AssetResource;
use App\Models\Asset;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * JSON API for the mobile asset intake app.
 *
 * Unlike the web AssetController (self-service, scoped to the logged-in
 * user's own assets), intake staff log assets on behalf of whoever it's being
 * assigned to, so this lists across all assets rather than just Auth::id().
 * Every authenticated regular user has the same read/write access here —
 * there's no sub-role distinction on this side of the login.
 */
class AssetApiController extends Controller
{
    private const MAX_PER_PAGE = 100;

    /**
     * GET /api/assets — most recent intakes first, optionally filtered by status.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 25);
        $perPage = max(1, min($perPage, self::MAX_PER_PAGE));

        $query = Asset::query();

        if ($status = $request->query('status')) {
            if (in_array($status, Asset::STATUSES, true)) {
                $query->where('status', $status);
            }
        }

        $assets = $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();

        return response()->json([
            'data' => AssetResource::collection($assets->items())->resolve(),
            'meta' => [
                'page' => $assets->currentPage(),
                'per_page' => $assets->perPage(),
                'total' => $assets->total(),
                'last_page' => $assets->lastPage(),
            ],
            'links' => [
                'prev' => $assets->previousPageUrl(),
                'next' => $assets->nextPageUrl(),
            ],
        ]);
    }

    /**
     * POST /api/assets — 201 with the created asset and a Location header.
     */
    public function store(AssetIntakeRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['status'] ??= 'configuration';
        $data['condition'] ??= 'new';
        $data['value'] ??= 0;
        $data['received_at'] ??= now()->toDateString();

        $asset = Asset::create($data);

        AuditLog::record('created', $asset, "Logged intake of “{$asset->name}” via mobile app.");

        return AssetResource::make($asset)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED)
            ->header('Location', route('api.assets.show', $asset));
    }

    /**
     * GET /api/assets/{asset}
     */
    public function show(Asset $asset): AssetResource
    {
        return AssetResource::make($asset);
    }

    /**
     * PUT|PATCH /api/assets/{asset}
     */
    public function update(AssetIntakeRequest $request, Asset $asset): AssetResource
    {
        $asset->update($request->validated());

        AuditLog::record('updated', $asset, "Updated asset “{$asset->name}” via mobile app.");

        return AssetResource::make($asset->fresh());
    }
}
