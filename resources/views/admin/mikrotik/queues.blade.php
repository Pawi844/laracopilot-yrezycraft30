@extends('layouts.admin')
@section('title','Queues — ' . $router->name)
@section('page-title','Queue Management')
@section('page-subtitle', $router->name)
@section('content')
@include('admin.mikrotik._nav')
<div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-4">
    <div class="px-4 py-3 border-b border-gray-100"><h3 class="text-gray-800 font-bold text-sm">Simple Queues ({{ count($simple) }})</h3></div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead style="background:#f8fafc"><tr>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Name</th>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Target</th>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Max Limit ↓/↑</th>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Burst ↓/↑</th>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Priority</th>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Status</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($simple as $q)
                <tr class="hover:bg-orange-50/20">
                    <td class="px-4 py-2.5 text-gray-800 font-semibold text-xs">{{ $q['name'] ?? '-' }}</td>
                    <td class="px-4 py-2.5 text-blue-600 font-mono text-xs">{{ $q['target'] ?? '-' }}</td>
                    <td class="px-4 py-2.5 text-xs"><span class="text-green-600">{{ $q['max-limit'] ?? '0/0' }}</span></td>
                    <td class="px-4 py-2.5 text-xs text-purple-600 font-mono">{{ $q['burst-limit'] ?? '0/0' }}</td>
                    <td class="px-4 py-2.5 text-gray-500 text-xs">{{ $q['priority'] ?? '8' }}</td>
                    <td class="px-4 py-2.5"><span class="px-2 py-0.5 rounded-full text-xs {{ (isset($q['disabled']) && $q['disabled'] === 'true') ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">{{ (isset($q['disabled']) && $q['disabled'] === 'true') ? 'Off' : 'On' }}</span></td>
                </tr>
                @empty
                <tr><td colspan="6" class="py-8 text-center text-gray-400">No simple queues</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
