@extends('layouts.admin')
@section('title','Edit Plan')
@section('page-title','Edit Plan')
@section('content')
<div class="max-w-2xl">
<div class="bg-slate-800 border border-slate-700 rounded-xl p-6">
    <form action="{{ route('admin.plans.update', $plan->id) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-xs font-semibold text-slate-300 mb-1">Plan Name</label>
                <input type="text" name="name" value="{{ old('name',$plan->name) }}" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Price (KES)</label>
                <input type="number" name="price" value="{{ old('price',$plan->price) }}" step="0.01" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Billing Cycle</label>
                <select name="billing_cycle" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    @foreach(['hourly','daily','weekly','monthly','quarterly','yearly'] as $c)
                    <option value="{{ $c }}" {{ old('billing_cycle',$plan->billing_cycle) === $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Download Speed</label>
                <input type="text" name="speed_download" value="{{ old('speed_download',$plan->speed_download) }}" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none font-mono">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Upload Speed</label>
                <input type="text" name="speed_upload" value="{{ old('speed_upload',$plan->speed_upload) }}" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none font-mono">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Profile Name</label>
                <input type="text" name="profile_name" value="{{ old('profile_name',$plan->profile_name) }}" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none font-mono">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Data Limit</label>
                <input type="text" name="data_limit" value="{{ old('data_limit',$plan->data_limit) }}" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Burst Limit</label>
                <input type="text" name="burst_limit" value="{{ old('burst_limit',$plan->burst_limit) }}" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none font-mono">
            </div>
            <div class="flex items-center">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="active" value="1" {{ old('active',$plan->active) ? 'checked' : '' }} class="rounded">
                    <span class="text-slate-300 text-sm">Active</span>
                </label>
            </div>
        </div>
        <div class="flex justify-end space-x-3 pt-2">
            <a href="{{ route('admin.plans.index') }}" class="px-4 py-2 border border-slate-600 text-slate-300 rounded-lg text-sm hover:bg-slate-700">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-sky-600 text-white rounded-lg text-sm font-semibold hover:bg-sky-700"><i class="fas fa-save mr-1"></i>Update Plan</button>
        </div>
    </form>
</div>
</div>
@endsection
