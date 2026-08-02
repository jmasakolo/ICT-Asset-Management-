@extends('layouts.app')

@section('title', 'New task · To Do')

@section('header-actions')
    <a class="btn btn-ghost" href="{{ route('tasks.index') }}">Back to list</a>
@endsection

@section('content')
    <h1 class="page-title">New task</h1>

    @include('tasks._form', [
        'task' => $task,
        'action' => route('tasks.store'),
        'method' => 'POST',
        'submit' => 'Create task',
    ])
@endsection
