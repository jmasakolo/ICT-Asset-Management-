@extends('layouts.app')

@section('title', $task->title . ' · To Do')

@section('header-actions')
    <a class="btn btn-ghost" href="{{ route('tasks.index') }}">Back to list</a>
@endsection

@section('content')
    <div class="card">
        <h1 class="page-title @if ($task->is_done) is-done-title @endif">{{ $task->title }}</h1>

        <dl class="detail-grid">
            <dt>Status</dt>
            <dd>
                @if ($task->is_done)
                    <span class="badge badge-done">Completed</span>
                    @if ($task->completed_at)
                        <span class="meta meta-muted">on {{ $task->completed_at->format('j M Y, H:i') }}</span>
                    @endif
                @else
                    <span class="badge badge-open">Open</span>
                @endif
            </dd>

            <dt>Priority</dt>
            <dd><span class="badge badge-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span></dd>

            <dt>Due date</dt>
            <dd>
                @if ($task->due_date)
                    {{ $task->due_date->format('j M Y') }}
                    @if ($task->isOverdue())
                        <span class="badge badge-overdue">Overdue</span>
                    @elseif ($task->isDueToday())
                        <span class="badge badge-today">Today</span>
                    @endif
                @else
                    <span class="meta meta-muted">Not set</span>
                @endif
            </dd>

            <dt>Notes</dt>
            <dd>
                @if ($task->notes)
                    <p class="notes">{{ $task->notes }}</p>
                @else
                    <span class="meta meta-muted">None</span>
                @endif
            </dd>

            <dt>Created</dt>
            <dd class="meta">{{ $task->created_at->format('j M Y, H:i') }}</dd>

            <dt>Last updated</dt>
            <dd class="meta">{{ $task->updated_at->format('j M Y, H:i') }}</dd>
        </dl>

        <div class="form-actions">
            <form action="{{ route('tasks.toggle', $task) }}" method="POST">
                @csrf
                @method('PATCH')
                <button class="btn btn-primary" type="submit">
                    {{ $task->is_done ? 'Reopen task' : 'Mark as done' }}
                </button>
            </form>

            <a class="btn btn-ghost" href="{{ route('tasks.edit', $task) }}">Edit</a>

            <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                  onsubmit="return confirm('Delete this task? This cannot be undone.');">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger" type="submit">Delete</button>
            </form>
        </div>
    </div>
@endsection
