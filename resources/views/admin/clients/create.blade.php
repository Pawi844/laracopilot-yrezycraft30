@extends('layouts.admin')
@section('title','Add Client')
@section('page-title','Add New Client')
@section('page-subtitle','Create a new subscriber account')
@section('content')
<div class="max-w-3xl">
<form action="{{ route('admin.clients.store') }}" method="POST" class="space-y-4">
    @csrf
    <!-- Personal Info -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 sm:p-6">
        <h3 class="text-gray-800 font-bold text-sm mb-4"><i class="fas fa-user text-orange-500 mr-2"></i>Personal Information</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">First Name *</label>
                <input type="text" name="first_name" value="{{ old('first_name') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Last Name *</label>
                <input type="text" name="last_name" value="{{ old('last_name') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+254712345678" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
        </div>
    </div>
    <!-- Account -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 sm:p-6">
        <h3 class="text-gray-800 font-bold text-sm mb-4"><i class="fas fa-key text-blue-500 mr-2"></i>Account Credentials</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Username *</label>
                <input type="text" name="username" value="{{ old('username') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
                @error('username')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Password *</label>
                <input type="text" name="password" value="{{ old('password') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Status *</label>
                <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
                    @foreach(['active'=>'Active','pending'=>'Pending','inactive'=>'Inactive','suspended'=>'Suspended'] as $v=>$l)
                    <option value="{{ $v }}" {{ old('status','active')===$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Expiry Date</label>
                <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
        </div>
    </div>
    <!-- Network / FAT -->
    <div class="bg-white rounded-xl border border-orange-100 shadow-sm p-5 sm:p-6">
        <h3 class="text-gray-800 font-bold text-sm mb-4"><i class="fas fa-project-diagram text-orange-500 mr-2"></i>Network & FAT Assignment</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Connection Type *</label>
                <select name="connection_type" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
                    @foreach(['pppoe'=>'PPPoE','hotspot'=>'Hotspot','static'=>'Static IP','dhcp'=>'DHCP'] as $v=>$l)
                    <option value="{{ $v }}" {{ old('connection_type','pppoe')===$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Plan</label>
                <select name="plan_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- No plan --</option>
                    @foreach($plans as $p)<option value="{{ $p->id }}" {{ old('plan_id')==$p->id?'selected':'' }}>{{ $p->name }} — KES {{ number_format($p->price) }}</option>@endforeach
                </select>
            </div>
            <!-- FAT Node Assignment -->
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1"><i class="fas fa-project-diagram text-orange-400 mr-1"></i>FAT Node Assignment <span class="font-normal text-gray-400">(Fiber Access Terminal)</span></label>
                <select name="fat_node_id" id="fat_select" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" onchange="showFatInfo(this.value)">
                    <option value="">-- Unassigned --</option>
                    @foreach($fatNodes as $f)
                    @php $slots=$f->available_slots; $pct=$f->usage_percent; @endphp
                    <option value="{{ $f->id }}" {{ old('fat_node_id')==$f->id?'selected':'' }}
                        data-slots="{{ $slots }}" data-pct="{{ $pct }}" data-name="{{ $f->name }}" data-max="{{ $f->max_onu }}" data-used="{{ $f->used_onu }}"
                        {{ $slots==0?'disabled':'' }}>
                        {{ $f->code }} — {{ $f->name }} ({{ $f->used_onu }}/{{ $f->max_onu }} · {{ $slots }} free){{ $slots==0?' [FULL]':'' }}
                    </option>
                    @endforeach
                </select>
                <div id="fat-info" class="hidden mt-2 bg-orange-50 border border-orange-200 rounded-lg p-3 text-xs"></div>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">NAS</label>
                <select name="nas_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- Select NAS --</option>
                    @foreach($nas as $n)<option value="{{ $n->id }}" {{ old('nas_id')==$n->id?'selected':'' }}>{{ $n->shortname }} ({{ $n->nasname }})</option>@endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Static IP</label>
                <input type="text" name="static_ip" value="{{ old('static_ip') }}" placeholder="Leave blank for dynamic" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">MAC Address</label>
                <input type="text" name="mac_address" value="{{ old('mac_address') }}" placeholder="AA:BB:CC:DD:EE:FF" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
        </div>
    </div>
    <div class="flex flex-col sm:flex-row justify-between gap-2">
        <a href="{{ route('admin.clients.index') }}" class="text-center px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50">← Back</a>
        <button type="submit" class="px-6 py-2.5 text-white rounded-xl text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-save mr-1"></i>Create Client</button>
    </div>
</form>
</div>
<script>
const fatData = @json($fatNodes->keyBy('id'));
function showFatInfo(id) {
    const d = document.getElementById('fat-info');
    if (!id || !fatData[id]) { d.classList.add('hidden'); return; }
    const f = fatData[id];
    const pct = f.usage_percent; const color = pct>=90?'red':(pct>=70?'yellow':'green');
    d.classList.remove('hidden');
    d.innerHTML = `<strong>${f.name}</strong> · Code: <span class="font-mono">${f.code}</span> · ${f.used_onu}/${f.max_onu} ONUs used · <strong class="text-${color}-600">${f.available_slots} slots free</strong>`;
}
const sel = document.getElementById('fat_select');
if (sel && sel.value) showFatInfo(sel.value);
</script>
@endsection
