@extends('layouts.admin')

@section('title', 'Audit Logs')
@section('header', 'Audit Logs')
@section('subheader', 'Logged in as ' . auth()->guard('admin')->user()->email)
@section('active', 'audit-logs')

@section('vite')
    @vite(['resources/css/app.css'])
@endsection

@section('content')
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-200 text-gray-500">
                <tr>
                    <th class="px-4 py-3 font-medium">When</th>
                    <th class="px-4 py-3 font-medium">Actor</th>
                    <th class="px-4 py-3 font-medium">Action</th>
                    <th class="px-4 py-3 font-medium">Subject</th>
                    <th class="px-4 py-3 font-medium">Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr class="border-b border-gray-100 last:border-0">
                        <td class="px-4 py-3 whitespace-nowrap">{{ $log->created_at->format('M j, Y g:ia') }}</td>
                        <td class="px-4 py-3">{{ $log->actor_label }} <span class="text-gray-400">({{ $log->actor_type }})</span></td>
                        <td class="px-4 py-3 capitalize">{{ $log->action }}</td>
                        <td class="px-4 py-3">{{ $log->subject_type ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $log->description }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            No activity logged yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
@endsection
