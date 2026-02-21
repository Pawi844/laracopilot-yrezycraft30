@extends('layouts.admin')
@section('title','Hotspot Plans')
@section('page-title','Hotspot Plans')
@section('page-subtitle','WiFi hotspot voucher and session plans')
@section('content')
<div class="flex justify-between items-center mb-4">
    <div class="flex space-x-2">
        <a href="{{ route('admin.plans.index') }}" class="border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-gray-50">PPPoE</a>
        <a href="{{ route('admin.hotspot.index') }}" class="text-white px-3 py-1.5 rounded-lg text-xs font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)">Hotspot</a>
    </div>
    <a href="{{ route('admin.hotspot.create') }}" class="text-white px-4 py-2 rounded-lg text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)">
        <i class="fas fa-plus mr-1"></i>Add Hotspot Plan
    </a>
</div>
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead style="background:#f8fafc">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Plan Name</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Price</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Validity</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Speed ↓/↑</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Profile</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($plans as $plan)
            <tr class="hover:bg-orange-50/30 transition-colors">
                <td class="px-4 py-3">
                    <p class="text-gray-800 font-semibold">{{ $plan->name }}</p>
                    <span class="bg-orange-100 text-orange-700 text-xs px-1.5 py-0.5 rounded font-medium">HOTSPOT</span>
                </td>
                <td class="px-4 py-3 text-green-600 font-bold">KES {{ number_format($plan->price) }}</td>
                <td class="px-4 py-3 text-gray-600 text-xs capitalize">{{ $plan->billing_cycle }}</td>
                <td class="px-4 py-3 text-gray-600 text-xs font-mono">{{ $plan->speed_download ?? '?' }}↓ / {{ $plan->speed_upload ?? '?' }}↑</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $plan->data_limit ?? 'Unlimited' }}</td>
                <td class="px-4 py-3 text-gray-400 text-xs font-mono">{{ $plan->profile_name ?? '-' }}</td>
                <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $plan->active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $plan->active ? 'Active' : 'Inactive' }}</span></td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('admin.hotspot.edit', $plan->id) }}" class="text-blue-600 hover:text-blue-800 text-xs"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.hotspot.destroy', $plan->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Delete plan?')" class="text-red-500 hover:text-red-700 text-xs ml-1"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-4 py-12 text-center text-gray-400">
                <i class="fas fa-wifi text-4xl mb-2 block text-gray-200"></i>No hotspot plans yet. <a href="{{ route('admin.hotspot.create') }}" class="text-orange-500 font-semibold">Create one →</a>
            </td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">{{ $plans->links() }}</div>
</div>
@endsection
