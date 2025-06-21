{{-- resources/views/activity-logs/index.blade.php --}}
@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Activity Logs') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3>Activity Logs (Total: {{ $logs->total() }})</h3>

                    <table class="table-auto w-full mt-4">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Date</th>
                                <th class="px-4 py-2">User</th>
                                <th class="px-4 py-2">Action</th>
                                <th class="px-4 py-2">Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td class="border px-4 py-2">{{ $log->created_at }}</td>
                                    <td class="border px-4 py-2">{{ optional($log->user)->name ?? 'System' }}</td>
                                    <td class="border px-4 py-2">{{ $log->action }}</td>
                                    <td class="border px-4 py-2">{{ $log->description }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection