@extends('layouts.app')

@section('title', 'Edit task · To Do')

@section('header-actions')
    <a class="btn btn-ghost" href="{{ route('tasks.index') }}">Back to list</a>
@endsection

@section('content')
    <h1 class="page-title">Edit task</h1>

    @include('tasks._form', [
        'task' => $task,
        'action' => route('tasks.update', $task),
        'method' => 'PUT',
        'submit' => 'Save changes',
    ])
@endsection
