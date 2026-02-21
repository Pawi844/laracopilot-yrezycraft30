@extends('layouts.admin')
@section('title','Edit Hotspot Plan')
@section('page-title','Edit Hotspot Plan')
@section('content')
<div class="max-w-2xl">
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
    <form action="{{ route('admin.hotspot.update', $plan->id) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2"><label class="block text-xs font-semibold text-gray-600 mb-1">Plan Name</label><input type="text" name="name" value="{{ old('name',$plan->name) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Price (KES)</label><input type="number" name="price" value="{{ old('price',$plan->price) }}" step="0.01" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Validity</label>
                <select name="billing_cycle" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    @foreach(['hourly','daily','weekly','monthly'] as $c)
                    <option value="{{ $c }}" {{ old('billing_cycle',$plan->billing_cycle) === $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Download Speed</label><input type="text" name="speed_download" value="{{ old('speed_download',$plan->speed_download) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Upload Speed</label><input type="text" name="speed_upload" value="{{ old('speed_upload',$plan->speed_upload) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Data Limit</label><input type="text" name="data_limit" value="{{ old('data_limit',$plan->data_limit) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Profile Name</label><input type="text" name="profile_name" value="{{ old('profile_name',$plan->profile_name) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div class="flex items-center">
                <label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" name="active" value="1" {{ old('active',$plan->active) ? 'checked' : '' }} class="rounded accent-orange-500"><span class="text-gray-600 text-sm">Active</span></label>
            </div>
        </div>
        <div class="flex justify-end space-x-3 pt-2">
            <a href="{{ route('admin.hotspot.index') }}" class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-5 py-2.5 text-white rounded-lg text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-save mr-1"></i>Update Plan</button>
        </div>
    </form>
</div>
</div>
@endsection
