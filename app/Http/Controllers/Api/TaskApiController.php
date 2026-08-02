<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TaskApiRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * JSON API for tasks, serving the Ionic client.
 *
 * Mirrors the web TaskController's behaviour exactly — same filters, same sort
 * keys, same validation, same derivation of completed_at — so the two surfaces
 * can't drift apart. Anything that would be a redirect on the web is a status
 * code here.
 */
class TaskApiController extends Controller
{
    private const FILTERS = ['all', 'active', 'done'];

    /** Sort keys mapped to [column, direction]; 'priority' needs a CASE scope. */
    private const SORTS = [
        'due' => ['due_date', 'asc'],
        'created' => ['created_at', 'desc'],
        'title' => ['title', 'asc'],
        'priority' => null,
    ];

    private const MAX_PER_PAGE = 100;

    /**
     * GET /api/tasks
     *
     * Query: filter=all|active|done, sort=due|priority|title|created,
     *        per_page=1..100, page=N
     */
    public function index(Request $request): JsonResponse
    {
        $filter = $this->pick($request->query('filter'), self::FILTERS, 'all');
        $sort = $this->pick($request->query('sort'), array_keys(self::SORTS), 'due');

        $perPage = (int) $request->integer('per_page', 25);
        $perPage = max(1, min($perPage, self::MAX_PER_PAGE));

        $query = Task::query();

        match ($filter) {
            'active' => $query->where('is_done', false),
            'done' => $query->where('is_done', true),
            'all' => null,
        };

        if ($sort === 'priority') {
            $query->orderByPriority();
        } else {
            [$column, $direction] = self::SORTS[$sort];
            $query->orderBy($column, $direction);
        }

        // Stable tiebreaker so repeat requests don't reshuffle equal-ranked rows.
        $tasks = $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();

        // The envelope is assembled by hand rather than left to the paginator,
        // whose default meta carries a rendered page-link array ("&laquo;
        // Previous") meant for Blade, not for API clients.
        return response()->json([
            'data' => TaskResource::collection($tasks->items())->resolve(),
            'meta' => [
                'filter' => $filter,
                'sort' => $sort,
                // Counts let a client render filter tabs without three extra calls.
                'counts' => [
                    'all' => Task::count(),
                    'active' => Task::where('is_done', false)->count(),
                    'done' => Task::where('is_done', true)->count(),
                ],
                'page' => $tasks->currentPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
                'last_page' => $tasks->lastPage(),
            ],
            'links' => [
                'prev' => $tasks->previousPageUrl(),
                'next' => $tasks->nextPageUrl(),
            ],
        ]);
    }

    /**
     * POST /api/tasks — 201 with the created task and a Location header.
     */
    public function store(TaskApiRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['priority'] ??= Task::DEFAULT_PRIORITY;
        $data['is_done'] ??= false;
        $data['completed_at'] = $data['is_done'] ? now() : null;

        $task = Task::create($data);

        return TaskResource::make($task)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED)
            ->header('Location', route('api.tasks.show', $task));
    }

    /**
     * GET /api/tasks/{task}
     */
    public function show(Task $task): TaskResource
    {
        return TaskResource::make($task);
    }

    /**
     * PUT|PATCH /api/tasks/{task}
     *
     * A partial update: fields the client omits are left alone. completed_at is
     * kept consistent with is_done rather than accepted from the payload.
     */
    public function update(TaskApiRequest $request, Task $task): TaskResource
    {
        $data = $request->validated();

        if (array_key_exists('is_done', $data) && $data['is_done'] !== $task->is_done) {
            $data['completed_at'] = $data['is_done'] ? now() : null;
        }

        $task->update($data);

        return TaskResource::make($task->fresh());
    }

    /**
     * DELETE /api/tasks/{task} — 204, no body.
     */
    public function destroy(Task $task): Response
    {
        $task->delete();

        return response()->noContent();
    }

    /**
     * PATCH /api/tasks/{task}/toggle
     *
     * Flips is_done. Exists so a client can complete a task without having to
     * know the current state first, avoiding a read-then-write race.
     */
    public function toggle(Task $task): TaskResource
    {
        $done = ! $task->is_done;

        $task->update([
            'is_done' => $done,
            'completed_at' => $done ? now() : null,
        ]);

        return TaskResource::make($task->fresh());
    }

    /**
     * Return $value only when it is one of $allowed, else $default.
     */
    private function pick(?string $value, array $allowed, string $default): string
    {
        return in_array($value, $allowed, true) ? $value : $default;
    }
}
