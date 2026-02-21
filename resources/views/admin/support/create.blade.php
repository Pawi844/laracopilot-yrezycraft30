@extends('layouts.admin')
@section('title','New Ticket')
@section('page-title','Create Support Ticket')
@section('page-subtitle','Log a complaint — assign to a client and technician')
@section('content')
<div class="max-w-2xl">
<form action="{{ route('admin.support.store') }}" method="POST" class="space-y-4">
    @csrf
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Subject *</label>
                <input type="text" name="subject" value="{{ old('subject') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" placeholder="Describe the issue briefly" required>
            </div>
            <!-- Client search -->
            <div class="col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Linked Client <span class="font-normal text-gray-400">(optional)</span></label>
                <select name="client_id" id="client_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" onchange="loadClientInfo(this.value)">
                    <option value="">-- Walk-in / Unlinked --</option>
                    @foreach($clients as $c)
                    <option value="{{ $c->id }}" {{ old('client_id')==$c->id?'selected':'' }}>{{ $c->username }} — {{ $c->first_name }} {{ $c->last_name }} ({{ $c->phone }})</option>
                    @endforeach
                </select>
                <div id="client-info" class="hidden mt-2 bg-blue-50 border border-blue-200 rounded-lg p-3 text-xs"></div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Assign Technician</label>
                <select name="technician_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- Unassigned --</option>
                    @foreach($technicians as $t)<option value="{{ $t->id }}" {{ old('technician_id')==$t->id?'selected':'' }}>{{ $t->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Priority *</label>
                <select name="priority" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
                    @foreach(['low'=>'Low','medium'=>'Medium','high'=>'High','urgent'=>'Urgent'] as $v=>$l)
                    <option value="{{ $v }}" {{ old('priority','medium')===$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Category</label>
                <select name="category" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- Select --</option>
                    @foreach($categories as $v=>$l)<option value="{{ $v }}" {{ old('category')===$v?'selected':'' }}>{{ $l }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Source</label>
                <select name="source" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    @foreach(['admin'=>'Admin','call_centre'=>'Call Centre','portal'=>'Client Portal','email'=>'Email','walk_in'=>'Walk-in'] as $v=>$l)
                    <option value="{{ $v }}" {{ old('source','admin')===$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">FAT Node</label>
                <select name="fat_node_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- None --</option>
                    @foreach($fatNodes as $f)<option value="{{ $f->id }}" {{ old('fat_node_id')==$f->id?'selected':'' }}>{{ $f->name }} ({{ $f->code }})</option>@endforeach
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Description *</label>
                <textarea name="description" rows="4" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" placeholder="Full description of the complaint / issue..." required>{{ old('description') }}</textarea>
            </div>
        </div>
    </div>
    <div class="flex justify-between">
        <a href="{{ route('admin.support.index') }}" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50">← Back</a>
        <button type="submit" class="px-6 py-2.5 text-white rounded-xl text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-save mr-1"></i>Create Ticket</button>
    </div>
</form>
</div>
<script>
const clients = @json($clients->keyBy('id'));
function loadClientInfo(id) {
    const info = document.getElementById('client-info');
    if (!id || !clients[id]) { info.classList.add('hidden'); return; }
    const c = clients[id];
    info.classList.remove('hidden');
    info.innerHTML = `<strong>${c.first_name} ${c.last_name}</strong> &nbsp;|&nbsp; ${c.username} &nbsp;|&nbsp; ${c.phone || 'No phone'}`;
}
</script>
@endsection
