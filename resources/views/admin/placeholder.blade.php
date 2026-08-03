@extends('layouts.admin')

@section('title', $title)
@section('header', $title)
@section('subheader', 'Logged in as ' . auth()->guard('admin')->user()->email)
@section('active', $active)

@section('vite')
    @vite(['resources/css/app.css'])
@endsection

@section('content')
    <div class="rounded-lg border border-dashed border-gray-300 bg-white p-12 text-center text-gray-400">
        <p class="text-sm">{{ $title }} is coming soon.</p>
    </div>
@endsection
