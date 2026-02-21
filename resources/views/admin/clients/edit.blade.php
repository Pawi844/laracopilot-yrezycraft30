@extends('layouts.admin')
@section('title','Edit Client')
@section('page-title','Edit Client')
@section('content')
<div class="max-w-3xl">
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
    <form action="{{ route('admin.clients.update', $client->id) }}" method="POST" class="space-y-5">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">First Name</label><input type="text" name="first_name" value="{{ old('first_name',$client->first_name) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Last Name</label><input type="text" name="last_name" value="{{ old('last_name',$client->last_name) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Email</label><input type="email" name="email" value="{{ old('email',$client->email) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Phone</label><input type="text" name="phone" value="{{ old('phone',$client->phone) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">New Password <span class="text-gray-400 font-normal">(leave blank)</span></label><input type="password" name="password" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Connection Type</label>
                <select name="connection_type" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    @foreach(['pppoe','hotspot','static'] as $t)<option value="{{ $t }}" {{ old('connection_type',$client->connection_type) === $t ? 'selected' : '' }}>{{ strtoupper($t) }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    @foreach(['active','inactive','suspended','expired'] as $s)<option value="{{ $s }}" {{ old('status',$client->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Plan</label>
                <select name="plan_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- No Plan --</option>
                    @foreach($plans as $p)<option value="{{ $p->id }}" {{ old('plan_id',$client->plan_id) == $p->id ? 'selected' : '' }}>{{ $p->name }} — KES {{ number_format($p->price) }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">NAS Server</label>
                <select name="nas_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- Select NAS --</option>
                    @foreach($nas as $n)<option value="{{ $n->id }}" {{ old('nas_id',$client->nas_id) == $n->id ? 'selected' : '' }}>{{ $n->name }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Static IP</label><input type="text" name="static_ip" value="{{ old('static_ip',$client->static_ip) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Expiry Date</label><input type="datetime-local" name="expiry_date" value="{{ old('expiry_date', $client->expiry_date ? $client->expiry_date->format('Y-m-d\TH:i') : '') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div class="col-span-2">
                <p class="text-xs font-semibold text-gray-600 mb-2">Notifications</p>
                <div class="flex space-x-4">
                    <label class="flex items-center space-x-1.5 cursor-pointer"><input type="checkbox" name="notify_sms" value="1" {{ $client->notify_sms ? 'checked' : '' }} class="rounded accent-orange-500"><span class="text-sm text-gray-600">SMS</span></label>
                    <label class="flex items-center space-x-1.5 cursor-pointer"><input type="checkbox" name="notify_email" value="1" {{ $client->notify_email ? 'checked' : '' }} class="rounded accent-orange-500"><span class="text-sm text-gray-600">Email</span></label>
                    <label class="flex items-center space-x-1.5 cursor-pointer"><input type="checkbox" name="notify_whatsapp" value="1" {{ $client->notify_whatsapp ? 'checked' : '' }} class="rounded accent-orange-500"><span class="text-sm text-gray-600">WhatsApp</span></label>
                </div>
            </div>
        </div>
        <div class="flex justify-end space-x-3 pt-2">
            <a href="{{ route('admin.clients.index') }}" class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-5 py-2.5 text-white rounded-lg text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-save mr-1"></i>Update Client</button>
        </div>
    </form>
</div>
</div>
@endsection
