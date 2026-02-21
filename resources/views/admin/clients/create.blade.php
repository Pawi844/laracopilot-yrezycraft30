@extends('layouts.admin')
@section('title','Add Client')
@section('page-title','Add New Client')
@section('content')
<div class="max-w-3xl">
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
    <form action="{{ route('admin.clients.store') }}" method="POST" class="space-y-5">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Username *</label><input type="text" name="username" value="{{ old('username') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none" required></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Password *</label><input type="password" name="password" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">First Name *</label><input type="text" name="first_name" value="{{ old('first_name') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Last Name *</label><input type="text" name="last_name" value="{{ old('last_name') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Email</label><input type="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Phone</label><input type="text" name="phone" value="{{ old('phone') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" placeholder="+254700000000"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">ID Number</label><input type="text" name="id_number" value="{{ old('id_number') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">County</label><input type="text" name="county" value="{{ old('county') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div class="col-span-2"><label class="block text-xs font-semibold text-gray-600 mb-1">Address</label><input type="text" name="address" value="{{ old('address') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Connection Type *</label>
                <select name="connection_type" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
                    @foreach(['pppoe','hotspot','static'] as $t)<option value="{{ $t }}" {{ old('connection_type') === $t ? 'selected' : '' }}>{{ strtoupper($t) }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Status *</label>
                <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
                    @foreach(['active','pending','inactive','suspended'] as $s)<option value="{{ $s }}" {{ old('status','active') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Plan</label>
                <select name="plan_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- No Plan --</option>
                    @foreach($plans as $p)<option value="{{ $p->id }}" {{ old('plan_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ strtoupper($p->type) }}) — KES {{ number_format($p->price) }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">NAS Server</label>
                <select name="nas_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- Select NAS --</option>
                    @foreach($nas as $n)<option value="{{ $n->id }}" {{ old('nas_id') == $n->id ? 'selected' : '' }}>{{ $n->name }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Router</label>
                <select name="router_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- Select Router --</option>
                    @foreach($routers as $r)<option value="{{ $r->id }}" {{ old('router_id') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Static IP</label><input type="text" name="static_ip" value="{{ old('static_ip') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">MAC Address</label><input type="text" name="mac_address" value="{{ old('mac_address') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Expiry Date</label><input type="datetime-local" name="expiry_date" value="{{ old('expiry_date') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Reseller</label>
                <select name="reseller_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- MtaaKonnect (Main) --</option>
                    @foreach($resellers as $r)<option value="{{ $r->id }}">{{ $r->company_name }}</option>@endforeach
                </select>
            </div>
            <div class="col-span-2">
                <p class="text-xs font-semibold text-gray-600 mb-2">Notification Preferences</p>
                <div class="flex space-x-4">
                    <label class="flex items-center space-x-1.5 cursor-pointer"><input type="checkbox" name="notify_sms" value="1" checked class="rounded accent-orange-500"><span class="text-gray-600 text-sm">SMS</span></label>
                    <label class="flex items-center space-x-1.5 cursor-pointer"><input type="checkbox" name="notify_email" value="1" checked class="rounded accent-orange-500"><span class="text-gray-600 text-sm">Email</span></label>
                    <label class="flex items-center space-x-1.5 cursor-pointer"><input type="checkbox" name="notify_whatsapp" value="1" class="rounded accent-orange-500"><span class="text-gray-600 text-sm">WhatsApp</span></label>
                </div>
            </div>
        </div>
        <div class="flex justify-end space-x-3 pt-2">
            <a href="{{ route('admin.clients.index') }}" class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-5 py-2.5 text-white rounded-lg text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-save mr-1"></i>Add Client</button>
        </div>
    </form>
</div>
</div>
@endsection
