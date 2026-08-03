@extends('layouts.app')

@section('title', 'Edit Task')

@section('content')
    <h2>Edit Task</h2>

    <form method="POST" action="{{ route('tasks.update', $task) }}">
        @csrf
        @method('PUT')
        @include('tasks._form')
    </form>
@endsection
