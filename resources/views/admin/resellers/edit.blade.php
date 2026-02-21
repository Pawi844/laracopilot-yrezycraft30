@extends('layouts.admin')
@section('title','Edit Reseller')
@section('page-title','Edit Reseller')
@section('content')
<div class="max-w-2xl">
<div class="bg-slate-800 border border-slate-700 rounded-xl p-6">
    <form action="{{ route('admin.resellers.update', $reseller->id) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-xs font-semibold text-slate-300 mb-1">Company Name</label>
                <input type="text" name="company_name" value="{{ old('company_name',$reseller->company_name) }}" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Contact Person</label>
                <input type="text" name="contact_name" value="{{ old('contact_name',$reseller->contact_name) }}" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email',$reseller->email) }}" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone',$reseller->phone) }}" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Commission Rate (%)</label>
                <input type="number" name="commission_rate" value="{{ old('commission_rate',$reseller->commission_rate) }}" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Status</label>
                <select name="status" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    @foreach(['pending','active','suspended'] as $s)
                    <option value="{{ $s }}" {{ old('status',$reseller->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-semibold text-slate-300 mb-2">Allowed Features</label>
                <div class="grid grid-cols-3 gap-2">
                    @foreach($features as $f)
                    <label class="flex items-center space-x-2 bg-slate-900 rounded-lg px-3 py-2 cursor-pointer hover:bg-slate-700">
                        <input type="checkbox" name="allowed_features[]" value="{{ $f }}" {{ in_array($f, $reseller->allowed_features ?? []) ? 'checked' : '' }} class="rounded">
                        <span class="text-slate-300 text-xs capitalize">{{ str_replace('_',' ',$f) }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="flex justify-end space-x-3 pt-2">
            <a href="{{ route('admin.resellers.index') }}" class="px-4 py-2 border border-slate-600 text-slate-300 rounded-lg text-sm hover:bg-slate-700">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-sky-600 text-white rounded-lg text-sm font-semibold hover:bg-sky-700"><i class="fas fa-save mr-1"></i>Update Reseller</button>
        </div>
    </form>
</div>
</div>
@endsection
