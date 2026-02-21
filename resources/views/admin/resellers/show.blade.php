@extends('layouts.admin')
@section('title','Reseller Details')
@section('page-title','Reseller Details')
@section('content')
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 text-center"><p class="text-2xl font-black text-white">{{ $reseller->clients_count }}</p><p class="text-slate-500 text-xs">Clients</p></div>
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 text-center"><p class="text-2xl font-black text-sky-400">{{ $reseller->operators_count }}</p><p class="text-slate-500 text-xs">Operators</p></div>
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 text-center"><p class="text-2xl font-black text-green-400">{{ $reseller->nas_count }}</p><p class="text-slate-500 text-xs">NAS Servers</p></div>
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 text-center"><p class="text-2xl font-black text-purple-400">{{ $reseller->plans_count }}</p><p class="text-slate-500 text-xs">Plans</p></div>
</div>
<div class="bg-slate-800 border border-slate-700 rounded-xl p-6">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h2 class="text-xl font-black text-white">{{ $reseller->company_name }}</h2>
            <p class="text-slate-400 text-sm">{{ $reseller->domain ?? 'No domain configured' }}</p>
        </div>
        <div class="flex space-x-2">
            <span class="px-3 py-1 rounded-full text-xs {{ $reseller->status === 'active' ? 'bg-green-900 text-green-300' : 'bg-red-900 text-red-300' }}">{{ ucfirst($reseller->status) }}</span>
            <a href="{{ route('admin.resellers.edit', $reseller->id) }}" class="bg-sky-600 text-white px-3 py-1 rounded-lg text-xs hover:bg-sky-700">Edit</a>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-6">
        <div class="space-y-3">
            <div><p class="text-slate-500 text-xs">Contact</p><p class="text-white">{{ $reseller->contact_name }}</p></div>
            <div><p class="text-slate-500 text-xs">Email</p><p class="text-white">{{ $reseller->email }}</p></div>
            <div><p class="text-slate-500 text-xs">Phone</p><p class="text-white">{{ $reseller->phone }}</p></div>
            <div><p class="text-slate-500 text-xs">Commission Rate</p><p class="text-yellow-400 font-bold">{{ $reseller->commission_rate }}%</p></div>
        </div>
        <div>
            <p class="text-slate-500 text-xs mb-2">Allowed Features</p>
            <div class="flex flex-wrap gap-2">
                @foreach($reseller->allowed_features ?? [] as $f)
                <span class="bg-sky-900 text-sky-300 text-xs px-2 py-1 rounded capitalize">{{ str_replace('_',' ',$f) }}</span>
                @endforeach
                @if(empty($reseller->allowed_features))
                <p class="text-slate-500 text-xs">No features assigned</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
