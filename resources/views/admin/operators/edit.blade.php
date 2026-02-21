@extends('layouts.admin')
@section('title','Edit Operator')
@section('page-title','Edit Operator')
@section('content')
<div class="max-w-2xl">
<div class="bg-slate-800 border border-slate-700 rounded-xl p-6">
    <form action="{{ route('admin.operators.update', $operator->id) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Full Name</label>
                <input type="text" name="name" value="{{ old('name',$operator->name) }}" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email',$operator->email) }}" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">New Password <span class="text-slate-500">(leave blank to keep)</span></label>
                <input type="password" name="password" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Role</label>
                <select name="role" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    @foreach(['admin','operator','support'] as $r)
                    <option value="{{ $r }}" {{ old('role',$operator->role) === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Reseller</label>
                <select name="reseller_id" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="">-- Main ISP --</option>
                    @foreach($resellers as $r)
                    <option value="{{ $r->id }}" {{ old('reseller_id',$operator->reseller_id) == $r->id ? 'selected' : '' }}>{{ $r->company_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="active" value="1" {{ old('active',$operator->active) ? 'checked' : '' }} class="rounded">
                    <span class="text-slate-300 text-sm">Active Account</span>
                </label>
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-semibold text-slate-300 mb-2">Permissions</label>
                <div class="grid grid-cols-3 gap-2">
                    @foreach($allPermissions as $p)
                    <label class="flex items-center space-x-2 bg-slate-900 rounded-lg px-3 py-2 cursor-pointer hover:bg-slate-700">
                        <input type="checkbox" name="permissions[]" value="{{ $p }}" {{ in_array($p, $operator->permissions ?? []) ? 'checked' : '' }} class="rounded">
                        <span class="text-slate-300 text-xs capitalize">{{ str_replace('_',' ',$p) }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="flex justify-end space-x-3 pt-2">
            <a href="{{ route('admin.operators.index') }}" class="px-4 py-2 border border-slate-600 text-slate-300 rounded-lg text-sm hover:bg-slate-700">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-sky-600 text-white rounded-lg text-sm font-semibold hover:bg-sky-700"><i class="fas fa-save mr-1"></i>Update Operator</button>
        </div>
    </form>
</div>
</div>
@endsection
