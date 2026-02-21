@extends('layouts.admin')
@section('title','PPPoE Plans')
@section('page-title','PPPoE Plans')
@section('content')
<div class="flex justify-between items-center mb-4">
    <div class="flex space-x-2">
        <a href="{{ route('admin.plans.index') }}" class="bg-sky-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold">PPPoE</a>
        <a href="{{ route('admin.hotspot.index') }}" class="bg-slate-700 text-slate-300 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-slate-600">Hotspot</a>
    </div>
    <a href="{{ route('admin.plans.create') }}" class="bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-sky-700"><i class="fas fa-plus mr-1"></i>Add PPPoE Plan</a>
</div>
<div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-900">
            <tr>
                <th class="px-4 py-3 text-left text-xs text-slate-500 uppercase">Plan Name</th>
                <th class="px-4 py-3 text-left text-xs text-slate-500 uppercase">Type</th>
                <th class="px-4 py-3 text-left text-xs text-slate-500 uppercase">Price</th>
                <th class="px-4 py-3 text-left text-xs text-slate-500 uppercase">Speed ↓/↑</th>
                <th class="px-4 py-3 text-left text-xs text-slate-500 uppercase">Data Limit</th>
                <th class="px-4 py-3 text-left text-xs text-slate-500 uppercase">Profile</th>
                <th class="px-4 py-3 text-left text-xs text-slate-500 uppercase">Status</th>
                <th class="px-4 py-3 text-right text-xs text-slate-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-700">
            @forelse($plans as $plan)
            <tr class="hover:bg-slate-700/40">
                <td class="px-4 py-3">
                    <p class="text-white font-semibold">{{ $plan->name }}</p>
                    <p class="text-slate-500 text-xs capitalize">{{ $plan->billing_cycle }}</p>
                </td>
                <td class="px-4 py-3"><span class="bg-purple-900 text-purple-300 text-xs px-2 py-0.5 rounded uppercase">{{ $plan->type }}</span></td>
                <td class="px-4 py-3 text-green-400 font-bold">KES {{ number_format($plan->price) }}</td>
                <td class="px-4 py-3 text-slate-300 text-xs font-mono">{{ $plan->speed_download ?? '?' }}↓ / {{ $plan->speed_upload ?? '?' }}↑</td>
                <td class="px-4 py-3 text-slate-400 text-xs">{{ $plan->data_limit ?? 'Unlimited' }}</td>
                <td class="px-4 py-3 text-slate-400 text-xs font-mono">{{ $plan->profile_name ?? '-' }}</td>
                <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs {{ $plan->active ? 'bg-green-900 text-green-300' : 'bg-slate-700 text-slate-400' }}">{{ $plan->active ? 'Active' : 'Inactive' }}</span></td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('admin.plans.edit', $plan->id) }}" class="text-sky-400 hover:text-sky-300 text-xs"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.plans.destroy', $plan->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Delete plan?')" class="text-red-400 hover:text-red-300 text-xs ml-2"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-4 py-10 text-center text-slate-500">No PPPoE plans found. <a href="{{ route('admin.plans.create') }}" class="text-sky-400">Create one →</a></td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-slate-700">{{ $plans->links() }}</div>
</div>
@endsection
