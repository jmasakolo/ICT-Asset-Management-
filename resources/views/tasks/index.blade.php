@extends('layouts.app')

@section('title', 'To Do')

@section('header-actions')
    <a class="btn btn-ghost" href="{{ asset('downloads/todo-app.apk') }}">Download Android app</a>
    <a class="btn btn-primary" href="{{ route('tasks.create') }}">New task</a>
@endsection

@section('content')
    <div class="toolbar">
        <nav class="tabs" aria-label="Filter tasks">
            @foreach (['all' => 'All', 'active' => 'Active', 'done' => 'Done'] as $key => $label)
                <a class="tab @if ($filter === $key) is-active @endif"
                   href="{{ route('tasks.index', ['filter' => $key, 'sort' => $sort]) }}"
                   @if ($filter === $key) aria-current="page" @endif>
                    {{ $label }} <span class="count">{{ $counts[$key] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="sorts">
            <span class="sorts-label">Sort</span>
            @foreach (['due' => 'Due date', 'priority' => 'Priority', 'title' => 'Title', 'created' => 'Newest'] as $key => $label)
                <a class="sort @if ($sort === $key) is-active @endif"
                   href="{{ route('tasks.index', ['filter' => $filter, 'sort' => $key]) }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    @if ($tasks->isEmpty())
        <div class="card empty">
            @if ($counts['all'] === 0)
                <h2>Nothing here yet</h2>
                <p>Add your first task to get started.</p>
                <a class="btn btn-primary" href="{{ route('tasks.create') }}">New task</a>
            @else
                <h2>No {{ $filter }} tasks</h2>
                <p>Nothing matches this filter right now.</p>
                <a class="btn btn-ghost" href="{{ route('tasks.index') }}">Show all tasks</a>
            @endif
        </div>
    @else
        <ul class="card tasks">
            @foreach ($tasks as $task)
                <li class="task @if ($task->is_done) is-done @endif">
                    {{-- A real submit button rather than a JS-driven checkbox, so
                         one-click toggling works without any JavaScript. --}}
                    <form action="{{ route('tasks.toggle', $task) }}" method="POST" class="toggle-form">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="filter" value="{{ $filter }}">
                        <input type="hidden" name="sort" value="{{ $sort }}">
                        <button class="toggle" type="submit"
                                aria-pressed="{{ $task->is_done ? 'true' : 'false' }}"
                                title="{{ $task->is_done ? 'Mark as not done' : 'Mark as done' }}">
                            <span class="sr-only">
                                {{ $task->is_done ? 'Mark as not done' : 'Mark as done' }}: {{ $task->title }}
                            </span>
                        </button>
                    </form>

                    <div class="task-main">
                        <a class="task-title" href="{{ route('tasks.show', $task) }}">{{ $task->title }}</a>

                        <div class="task-meta">
                            <span class="badge badge-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span>

                            @if ($task->due_date)
                                @if ($task->isOverdue())
                                    <span class="badge badge-overdue">
                                        Overdue &middot; {{ $task->due_date->format('j M Y') }}
                                    </span>
                                @elseif ($task->isDueToday())
                                    <span class="badge badge-today">Due today</span>
                                @else
                                    <span class="meta">Due {{ $task->due_date->format('j M Y') }}</span>
                                @endif
                            @else
                                <span class="meta meta-muted">No due date</span>
                            @endif

                            @if ($task->notes)
                                <span class="meta meta-muted">&middot; has notes</span>
                            @endif
                        </div>
                    </div>

                    <div class="task-actions">
                        <a class="btn btn-ghost btn-sm" href="{{ route('tasks.edit', $task) }}">Edit</a>
                        <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                              onsubmit="return confirm('Delete this task? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>

        @if ($tasks->hasPages())
            <div class="pagination">
                @if ($tasks->onFirstPage())
                    <span class="page is-disabled">Previous</span>
                @else
                    <a class="page" href="{{ $tasks->previousPageUrl() }}" rel="prev">Previous</a>
                @endif

                <span class="page-info">
                    Page {{ $tasks->currentPage() }} of {{ $tasks->lastPage() }}
                    ({{ $tasks->total() }} tasks)
                </span>

                @if ($tasks->hasMorePages())
                    <a class="page" href="{{ $tasks->nextPageUrl() }}" rel="next">Next</a>
                @else
                    <span class="page is-disabled">Next</span>
                @endif
            </div>
        @endif
    @endif
@endsection
