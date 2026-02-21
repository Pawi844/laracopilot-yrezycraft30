@extends('layouts.admin')
@section('title','Edit Router')
@section('page-title','Edit Router')
@section('content')
<div class="max-w-2xl">
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
    <form action="{{ route('admin.routers.update', $router->id) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2"><label class="block text-xs font-semibold text-gray-600 mb-1">Router Name</label><input type="text" name="name" value="{{ old('name',$router->name) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">IP Address</label><input type="text" name="ip_address" value="{{ old('ip_address',$router->ip_address) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none" required></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">API Port</label><input type="number" name="api_port" value="{{ old('api_port',$router->api_port) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Username</label><input type="text" name="username" value="{{ old('username',$router->username) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Password <span class="text-gray-400 font-normal">(leave blank to keep)</span></label><input type="password" name="password" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">NAS Server</label>
                <select name="nas_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- Select NAS --</option>
                    @foreach($nas as $n)<option value="{{ $n->id }}" {{ old('nas_id',$router->nas_id) == $n->id ? 'selected' : '' }}>{{ $n->name }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Reseller</label>
                <select name="reseller_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- MtaaKonnect (Main) --</option>
                    @foreach($resellers as $r)<option value="{{ $r->id }}" {{ old('reseller_id',$router->reseller_id) == $r->id ? 'selected' : '' }}>{{ $r->company_name }}</option>@endforeach
                </select>
            </div>
        </div>
        <div class="flex justify-end space-x-3 pt-2">
            <a href="{{ route('admin.routers.index') }}" class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-5 py-2.5 text-white rounded-lg text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-save mr-1"></i>Update Router</button>
        </div>
    </form>
</div>
</div>
@endsection
