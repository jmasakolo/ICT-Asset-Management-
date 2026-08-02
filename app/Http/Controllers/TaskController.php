<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Filters the listing supports. Whitelisted so a hand-crafted query
     * string can't reach the database.
     */
    private const FILTERS = ['all', 'active', 'done'];

    /**
     * Sort keys mapped to [column, direction]. 'priority' maps to null because
     * it needs the model's orderByPriority scope (a CASE expression) instead.
     */
    private const SORTS = [
        'due' => ['due_date', 'asc'],
        'created' => ['created_at', 'desc'],
        'title' => ['title', 'asc'],
        'priority' => null,
    ];

    public function index(Request $request): View
    {
        $filter = $this->pick($request->query('filter'), self::FILTERS, 'all');
        $sort = $this->pick($request->query('sort'), array_keys(self::SORTS), 'due');

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
            // Postgres puts NULLs last on ASC, which is what we want for
            // undated tasks — they shouldn't crowd out real deadlines.
            $query->orderBy($column, $direction);
        }

        // Stable tiebreaker so repeat loads don't reshuffle equal-ranked rows.
        $tasks = $query->orderBy('id', 'desc')->paginate(25)->withQueryString();

        return view('tasks.index', [
            'tasks' => $tasks,
            'filter' => $filter,
            'sort' => $sort,
            'counts' => [
                'all' => Task::count(),
                'active' => Task::where('is_done', false)->count(),
                'done' => Task::where('is_done', true)->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('tasks.create', [
            'task' => new Task(['priority' => Task::DEFAULT_PRIORITY]),
        ]);
    }

    public function store(TaskRequest $request): RedirectResponse
    {
        $task = Task::create($this->attributes($request));

        return redirect()
            ->route('tasks.index')
            ->with('status', "Added “{$task->title}”.");
    }

    public function show(Task $task): View
    {
        return view('tasks.show', ['task' => $task]);
    }

    public function edit(Task $task): View
    {
        return view('tasks.edit', ['task' => $task]);
    }

    public function update(TaskRequest $request, Task $task): RedirectResponse
    {
        $task->update($this->attributes($request, $task));

        return redirect()
            ->route('tasks.index')
            ->with('status', "Updated “{$task->title}”.");
    }

    public function destroy(Task $task): RedirectResponse
    {
        $title = $task->title;
        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('status', "Deleted “{$title}”.");
    }

    /**
     * Flip a task's done state. Has its own route so the listing can offer a
     * one-click checkbox without routing through the edit form.
     */
    public function toggle(Request $request, Task $task): RedirectResponse
    {
        $done = ! $task->is_done;

        $task->update([
            'is_done' => $done,
            'completed_at' => $done ? now() : null,
        ]);

        // Preserve whichever filter/sort the user was looking at.
        return redirect()
            ->route('tasks.index', $request->only('filter', 'sort'))
            ->with('status', $done
                ? "Completed “{$task->title}”."
                : "Reopened “{$task->title}”.");
    }

    /**
     * Build the save payload, keeping completed_at consistent with is_done.
     * completed_at is derived here rather than accepted from the form, so the
     * two columns can never disagree.
     */
    private function attributes(TaskRequest $request, ?Task $existing = null): array
    {
        $data = $request->validated();
        $wasDone = $existing?->is_done ?? false;

        if ($data['is_done'] && ! $wasDone) {
            $data['completed_at'] = now();
        } elseif (! $data['is_done']) {
            $data['completed_at'] = null;
        }

        return $data;
    }

    /**
     * Return $value only when it is one of $allowed, else $default.
     */
    private function pick(?string $value, array $allowed, string $default): string
    {
        return in_array($value, $allowed, true) ? $value : $default;
    }
}
