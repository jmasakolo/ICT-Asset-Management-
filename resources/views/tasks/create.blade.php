@extends('layouts.app')

@section('title', 'New Task')

@section('content')
    <h2>New Task</h2>

    <form method="POST" action="{{ route('tasks.store') }}">
        @csrf
        @include('tasks._form')
    </form>
@endsection
