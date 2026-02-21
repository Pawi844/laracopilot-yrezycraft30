@extends('layouts.admin')
@section('title','IP Pools — ' . $router->name)
@section('page-title','IP Pools')
@section('page-subtitle', $router->name)
@section('content')
@include('admin.mikrotik._nav')
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-100"><h3 class="text-gray-800 font-bold text-sm">IP Pools ({{ count($pools) }})</h3></div>
    <table class="w-full">
        <thead style="background:#f8fafc"><tr>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Pool Name</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Range</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Next Pool</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($pools as $pool)
            <tr class="hover:bg-orange-50/20">
                <td class="px-4 py-3 text-gray-800 font-semibold text-sm">{{ $pool['name'] ?? '-' }}</td>
                <td class="px-4 py-3 text-blue-700 font-mono text-xs">{{ $pool['ranges'] ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $pool['next-pool'] ?? 'none' }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="px-4 py-12 text-center text-gray-400">No IP pools configured</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
