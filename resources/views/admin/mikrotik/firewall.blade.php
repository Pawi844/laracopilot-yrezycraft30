@extends('layouts.admin')
@section('title','Firewall — ' . $router->name)
@section('page-title','Firewall Rules')
@section('page-subtitle', $router->name)
@section('content')
@include('admin.mikrotik._nav')

@foreach([
    ['title' => 'Filter Rules ('.count($filter).')', 'data' => $filter, 'color' => 'red'],
    ['title' => 'NAT Rules ('.count($nat).')', 'data' => $nat, 'color' => 'blue'],
    ['title' => 'Mangle Rules ('.count($mangle).')', 'data' => $mangle, 'color' => 'orange'],
] as $section)
<div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-4">
    <div class="px-4 py-3 border-b border-gray-100">
        <h3 class="text-gray-800 font-bold text-sm">{{ $section['title'] }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead style="background:#f8fafc"><tr>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">#</th>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Chain</th>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Action</th>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Src Address</th>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Dst Address</th>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Protocol</th>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Comment</th>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Status</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($section['data'] as $i => $rule)
                <tr class="hover:bg-orange-50/20">
                    <td class="px-4 py-2 text-gray-400 text-xs">{{ $i+1 }}</td>
                    <td class="px-4 py-2 text-xs"><span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-semibold">{{ $rule['chain'] ?? '-' }}</span></td>
                    <td class="px-4 py-2 text-xs">
                        <span class="px-2 py-0.5 rounded text-xs font-bold
                            {{ in_array($rule['action'] ?? '', ['drop','reject']) ? 'bg-red-100 text-red-700' : '' }}
                            {{ ($rule['action'] ?? '') === 'accept' ? 'bg-green-100 text-green-700' : '' }}
                            {{ !in_array($rule['action'] ?? '', ['drop','reject','accept']) ? 'bg-gray-100 text-gray-600' : '' }}">
                            {{ strtoupper($rule['action'] ?? '-') }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-gray-500 font-mono text-xs">{{ $rule['src-address'] ?? 'any' }}</td>
                    <td class="px-4 py-2 text-gray-500 font-mono text-xs">{{ $rule['dst-address'] ?? 'any' }}</td>
                    <td class="px-4 py-2 text-gray-500 text-xs">{{ $rule['protocol'] ?? 'any' }}</td>
                    <td class="px-4 py-2 text-gray-400 text-xs">{{ $rule['comment'] ?? '-' }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-0.5 rounded-full text-xs {{ (isset($rule['disabled']) && $rule['disabled'] === 'true') ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                            {{ (isset($rule['disabled']) && $rule['disabled'] === 'true') ? 'Disabled' : 'Active' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-6 text-center text-gray-400 text-xs">No rules</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endforeach
@endsection
