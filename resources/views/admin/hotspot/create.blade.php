@extends('layouts.admin')
@section('title','Create Hotspot Plan')
@section('page-title','Create Hotspot Plan')
@section('content')
<div class="max-w-2xl">
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
    <form action="{{ route('admin.hotspot.store') }}" method="POST" class="space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2"><label class="block text-xs font-semibold text-gray-600 mb-1">Plan Name *</label><input type="text" name="name" value="{{ old('name') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" placeholder="e.g. 1 Hour Hotspot" required></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Price (KES) *</label><input type="number" name="price" value="{{ old('price') }}" step="0.01" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Validity *</label>
                <select name="billing_cycle" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
                    @foreach(['hourly','daily','weekly','monthly'] as $c)
                    <option value="{{ $c }}" {{ old('billing_cycle') === $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Download Speed</label><input type="text" name="speed_download" value="{{ old('speed_download') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none" placeholder="2M"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Upload Speed</label><input type="text" name="speed_upload" value="{{ old('speed_upload') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none" placeholder="1M"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Data Limit</label><input type="text" name="data_limit" value="{{ old('data_limit') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" placeholder="Unlimited or 500MB"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Session Timeout (sec)</label><input type="number" name="session_timeout" value="{{ old('session_timeout') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" placeholder="3600"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Idle Timeout (sec)</label><input type="number" name="idle_timeout" value="{{ old('idle_timeout') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" placeholder="300"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Hotspot Profile Name</label><input type="text" name="profile_name" value="{{ old('profile_name') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none" placeholder="hotspot-1hr"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Reseller</label>
                <select name="reseller_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- MtaaKonnect (Main) --</option>
                    @foreach($resellers as $r)<option value="{{ $r->id }}">{{ $r->company_name }}</option>@endforeach
                </select>
            </div>
            <div class="flex items-center col-span-2">
                <label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" name="active" value="1" checked class="rounded accent-orange-500"><span class="text-gray-600 text-sm">Active Plan</span></label>
            </div>
        </div>
        <div class="flex justify-end space-x-3 pt-2">
            <a href="{{ route('admin.hotspot.index') }}" class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-5 py-2.5 text-white rounded-lg text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-save mr-1"></i>Create Plan</button>
        </div>
    </form>
</div>
</div>
@endsection
