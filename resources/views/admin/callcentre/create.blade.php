@extends('layouts.admin')
@section('title','Log Call')
@section('page-title','Log a Call')
@section('page-subtitle','Record call details — auto-lookup client by phone')
@section('content')
<div class="max-w-2xl">
<form action="{{ route('admin.callcentre.store') }}" method="POST" class="space-y-4">
    @csrf
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
        <!-- Phone lookup -->
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Caller Phone Number *</label>
            <div class="flex space-x-2">
                <input type="text" name="caller_number" id="caller_number" value="{{ old('caller_number') }}" placeholder="+254712345678" class="flex-1 border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
                <button type="button" onclick="lookupPhone()" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700"><i class="fas fa-search mr-1"></i>Lookup</button>
            </div>
            <div id="client-lookup" class="hidden mt-2 bg-blue-50 border border-blue-200 rounded-xl p-3 text-xs"></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Linked Client</label>
                <select name="client_id" id="client_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- Unknown / Not a client --</option>
                    @foreach($clients as $c)
                    <option value="{{ $c->id }}" {{ old('client_id')==$c->id?'selected':'' }}>{{ $c->username }} — {{ $c->first_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Agent / Handler</label>
                <select name="agent_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- Self --</option>
                    @foreach($agents as $a)<option value="{{ $a->id }}" {{ old('agent_id')==$a->id?'selected':'' }}>{{ $a->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Direction *</label>
                <select name="direction" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="inbound" {{ old('direction','inbound')==='inbound'?'selected':'' }}>Inbound (Client called)</option>
                    <option value="outbound" {{ old('direction')==='outbound'?'selected':'' }}>Outbound (We called)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Call Status *</label>
                <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
                    @foreach(['answered'=>'Answered','missed'=>'Missed','voicemail'=>'Voicemail','dropped'=>'Dropped'] as $v=>$l)
                    <option value="{{ $v }}" {{ old('status','answered')===$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Duration (seconds)</label>
                <input type="number" name="duration_seconds" value="{{ old('duration_seconds',0) }}" min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Disposition / Outcome</label>
                <select name="disposition" id="disposition" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- Select --</option>
                    <option value="resolved">Resolved on call</option>
                    <option value="follow_up">Follow-up needed</option>
                    <option value="escalated">Escalated (creates ticket)</option>
                    <option value="no_action">No action needed</option>
                </select>
            </div>
        </div>
        <div id="ticket-note" class="hidden bg-orange-50 border border-orange-200 rounded-lg p-3 text-xs text-orange-800">
            <i class="fas fa-info-circle mr-1"></i>A support ticket will be automatically created from these notes.
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Call Notes</label>
            <textarea name="notes" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" placeholder="Summary of the call, issues discussed...">{{ old('notes') }}</textarea>
        </div>
    </div>
    <div class="flex justify-between">
        <a href="{{ route('admin.callcentre.index') }}" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600">← Back</a>
        <button type="submit" class="px-6 py-2.5 text-white rounded-xl text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-save mr-1"></i>Save Call Log</button>
    </div>
</form>
</div>
<script>
document.getElementById('disposition').addEventListener('change',function(){
    document.getElementById('ticket-note').classList.toggle('hidden', !['escalated','follow_up'].includes(this.value));
});
function lookupPhone() {
    const phone = document.getElementById('caller_number').value;
    if (!phone) return;
    fetch('{{ route("admin.callcentre.lookup") }}?phone='+encodeURIComponent(phone))
    .then(r=>r.json()).then(data=>{
        const div = document.getElementById('client-lookup');
        div.classList.remove('hidden');
        if (data.found) {
            div.className='mt-2 bg-green-50 border border-green-200 rounded-xl p-3 text-xs';
            div.innerHTML=`<strong>${data.name}</strong> (${data.username}) · Plan: ${data.plan} · Status: <strong>${data.status}</strong> · Expiry: ${data.expiry} · Open Tickets: ${data.open_tickets}`;
            // Auto-select in dropdown
            const sel = document.getElementById('client_id');
            for(let o of sel.options) if(o.value==data.id) { o.selected=true; break; }
        } else {
            div.className='mt-2 bg-red-50 border border-red-200 rounded-xl p-3 text-xs text-red-700';
            div.innerHTML='<i class="fas fa-times-circle mr-1"></i>No client found with this number.';
        }
    });
}
</script>
@endsection
