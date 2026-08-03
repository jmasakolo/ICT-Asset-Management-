@extends('layouts.app')

@section('title', 'Tasks')

@section('content')
    <p><a href="{{ route('tasks.create') }}">+ New task</a></p>

    @if ($tasks->isEmpty())
        <p>No tasks yet.</p>
    @else
        <ul>
            @foreach ($tasks as $task)
                <li>
                    <form method="POST" action="{{ route('tasks.toggle', $task) }}" style="display:inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit">{{ $task->is_done ? '☑' : '☐' }}</button>
                    </form>

                    <span style="{{ $task->is_done ? 'text-decoration:line-through' : '' }}">
                        {{ $task->title }}
                    </span>

                    ({{ $task->priority }}@if ($task->due_date), due {{ $task->due_date->format('Y-m-d') }}@endif)

                    <a href="{{ route('tasks.edit', $task) }}">Edit</a>

                    <form method="POST" action="{{ route('tasks.destroy', $task) }}" style="display:inline"
                          onsubmit="return confirm('Delete this task?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Delete</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif
@endsection
