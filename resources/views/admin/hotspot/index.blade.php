@extends('layouts.admin')
@section('title','Hotspot')
@section('page-title','Hotspot Management')
@section('page-subtitle','Manage WiFi hotspot plans, captive portal and active sessions')
@section('content')

<!-- Stats -->
<div class="grid grid-cols-3 gap-3 mb-4">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center space-x-3">
        <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-users text-green-600"></i>
        </div>
        <div><p class="text-gray-400 text-xs">Active Users</p><p class="text-2xl font-black text-gray-800">{{ $stats['active_users'] }}</p></div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center space-x-3">
        <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-tags text-blue-600"></i>
        </div>
        <div><p class="text-gray-400 text-xs">Plans</p><p class="text-2xl font-black text-gray-800">{{ $stats['total_plans'] }}</p></div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center space-x-3">
        <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-router text-orange-600"></i>
        </div>
        <div><p class="text-gray-400 text-xs">Routers</p><p class="text-2xl font-black text-gray-800">{{ $stats['routers'] }}</p></div>
    </div>
</div>

<!-- Quick actions -->
<div class="flex flex-wrap gap-2 mb-4">
    <a href="{{ route('admin.hotspot.create') }}" class="text-white px-4 py-2 rounded-lg text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)">
        <i class="fas fa-plus mr-1"></i>Add Plan
    </a>
    <a href="{{ route('admin.hotspot.captive') }}" class="border border-blue-200 text-blue-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-50">
        <i class="fas fa-mobile-alt mr-1"></i>Captive Portal
    </a>
    <a href="{{ route('admin.hotspot.captive.preview') }}" target="_blank" class="border border-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50">
        <i class="fas fa-eye mr-1"></i>Preview Login Page
    </a>
    <a href="{{ route('admin.sessions.index') }}" class="border border-green-200 text-green-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-50">
        <i class="fas fa-satellite-dish mr-1"></i>Live Sessions
    </a>
</div>

<!-- MikroTik Hotspot Setup Info -->
<div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
    <p class="text-blue-800 font-bold text-sm mb-1"><i class="fas fa-info-circle mr-1"></i>MikroTik Hotspot — Captive Portal URL</p>
    <p class="text-blue-700 text-xs mb-2">Set this URL as your hotspot login page in MikroTik → IP → Hotspot → Server Profiles → Login Page:</p>
    <div class="bg-white border border-blue-200 rounded-lg p-2 font-mono text-xs text-blue-800 break-all">{{ url('/hotspot') }}</div>
    <p class="text-blue-600 text-xs mt-1">Or upload <strong>login.html</strong> (downloaded from Captive Portal settings) via Winbox → Files → hotspot folder.</p>
</div>

<!-- Plans Table -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-100">
        <h3 class="font-bold text-gray-800 text-sm"><i class="fas fa-tags text-orange-500 mr-2"></i>Plans ({{ $plans->total() }})</h3>
    </div>
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead style="background:#f8fafc">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Plan Name</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 hidden sm:table-cell">Price</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 hidden md:table-cell">Speed</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 hidden md:table-cell">Duration</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Status</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
        @forelse($plans as $plan)
        <tr class="hover:bg-gray-50">
            <td class="px-4 py-3">
                <p class="font-bold text-gray-800 text-sm">{{ $plan->name }}</p>
                @if($plan->description)<p class="text-gray-400 text-xs">{{ Str::limit($plan->description,50) }}</p>@endif
            </td>
            <td class="px-4 py-3 hidden sm:table-cell">
                <span class="font-bold text-green-700">KES {{ number_format($plan->price ?? 0) }}</span>
            </td>
            <td class="px-4 py-3 hidden md:table-cell">
                <span class="text-xs text-gray-600">{{ $plan->speed_limit ?? '—' }}</span>
            </td>
            <td class="px-4 py-3 hidden md:table-cell">
                <span class="text-xs text-gray-600">{{ $plan->duration_days ? $plan->duration_days.'d' : '—' }}</span>
            </td>
            <td class="px-4 py-3">
                @if($plan->active ?? true)
                <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full font-semibold">Active</span>
                @else
                <span class="bg-gray-100 text-gray-500 text-xs px-2 py-0.5 rounded-full">Inactive</span>
                @endif
            </td>
            <td class="px-4 py-3">
                <div class="flex justify-end gap-1">
                    <a href="{{ route('admin.hotspot.edit',$plan->id) }}" class="w-7 h-7 flex items-center justify-center rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-100" title="Edit">
                        <i class="fas fa-edit text-xs"></i>
                    </a>
                    <form action="{{ route('admin.hotspot.destroy',$plan->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Delete this plan?')" class="w-7 h-7 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-100" title="Delete">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="px-4 py-16 text-center">
                <i class="fas fa-wifi text-5xl text-gray-200 mb-3 block"></i>
                <p class="text-gray-500 font-semibold">No hotspot plans yet.</p>
                <a href="{{ route('admin.hotspot.create') }}" class="text-orange-500 font-semibold text-sm hover:underline">Create one →</a>
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>
    </div>
    @if($plans->hasPages())
    <div class="px-4 py-3 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <p class="text-gray-400 text-xs">Showing {{ $plans->firstItem() }}–{{ $plans->lastItem() }} of {{ $plans->total() }}</p>
        {{ $plans->links() }}
    </div>
    @endif
</div>
@endsection
