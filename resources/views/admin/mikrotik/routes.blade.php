@extends('layouts.admin')
@section('title','Routes — ' . $router->name)
@section('page-title','Routing Table')
@section('page-subtitle', $router->name)
@section('content')
@include('admin.mikrotik._nav')
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full">
        <thead style="background:#f8fafc"><tr>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Destination</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Gateway</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Interface</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Distance</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Status</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($routes as $r)
            <tr class="hover:bg-orange-50/20">
                <td class="px-4 py-3 text-blue-700 font-mono text-xs font-bold">{{ $r['dst-address'] ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ $r['gateway'] ?? 'local' }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $r['gateway-status'] ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $r['distance'] ?? '-' }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs {{ (isset($r['active']) && $r['active'] === 'true') ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ (isset($r['active']) && $r['active'] === 'true') ? 'Active' : 'Inactive' }}
                    </span>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="py-12 text-center text-gray-400">No routes</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
