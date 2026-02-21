@extends('layouts.admin')
@section('title','Notification Schedule')
@section('page-title','Notification Schedule')
@section('page-subtitle','Set when each notification should be sent — immediately, days before expiry, etc.')
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    <!-- Existing Schedules -->
    <div class="space-y-3">
        <h3 class="text-gray-800 font-bold">Active Schedules</h3>
        @forelse($schedules as $s)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <div class="flex justify-between items-start">
                <div>
                    <div class="flex items-center space-x-2 mb-1">
                        <span class="text-xs px-2 py-0.5 rounded-full font-bold {{ $s->channel==='sms'?'bg-green-100 text-green-700':($s->channel==='email'?'bg-blue-100 text-blue-700':'bg-green-100 text-green-800') }}">{{ strtoupper($s->channel) }}</span>
                        <span class="text-gray-700 font-semibold text-sm">{{ $events[$s->event] ?? $s->event }}</span>
                        @if(!$s->active)<span class="text-xs bg-red-100 text-red-600 px-1.5 py-0.5 rounded">Disabled</span>@endif
                    </div>
                    <p class="text-gray-500 text-xs">
                        @if($s->timing === 'immediate') Send immediately when event occurs
                        @elseif($s->timing === 'days_before') Send {{ $s->days_offset }} day(s) BEFORE expiry at {{ $s->send_at_time }}
                        @elseif($s->timing === 'days_after') Send {{ $s->days_offset }} day(s) AFTER event at {{ $s->send_at_time }}
                        @else Send on the day of event at {{ $s->send_at_time }}
                        @endif
                    </p>
                </div>
                <div class="flex space-x-2">
                    <form action="{{ route('admin.notifications.schedule.update', $s->id) }}" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="active" value="{{ $s->active?0:1 }}">
                        <button class="text-xs px-2 py-1 rounded {{ $s->active?'bg-yellow-100 text-yellow-700':'bg-green-100 text-green-700' }}">
                            {{ $s->active?'Disable':'Enable' }}
                        </button>
                    </form>
                    <form action="{{ route('admin.notifications.schedule.destroy', $s->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="text-red-400 hover:text-red-600 text-xs px-2 py-1"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl border border-gray-100 p-8 text-center text-gray-400">
            <i class="fas fa-clock text-4xl mb-2 block text-gray-200"></i>
            No schedules yet. Add one on the right.
        </div>
        @endforelse
    </div>

    <!-- Add Schedule Form -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h3 class="text-gray-800 font-bold mb-4"><i class="fas fa-plus-circle text-orange-500 mr-2"></i>Add Notification Schedule</h3>
        <form action="{{ route('admin.notifications.schedule.store') }}" method="POST" class="space-y-4">
            @csrf
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Event</label>
                <select name="event" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
                    <option value="">-- Select event --</option>
                    @foreach($events as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Channel</label>
                <select name="channel" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
                    <option value="sms">SMS</option>
                    <option value="email">Email</option>
                    <option value="whatsapp">WhatsApp</option>
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Timing</label>
                <select name="timing" id="timing" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
                    <option value="immediate">Immediately when event occurs</option>
                    <option value="days_before">X days BEFORE expiry</option>
                    <option value="days_after">X days AFTER event</option>
                    <option value="on_day">On the day of event</option>
                </select>
            </div>
            <div id="days-field" class="hidden">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Days Offset</label>
                <input type="number" name="days_offset" min="0" max="90" value="3" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Send at Time</label>
                <input type="time" name="send_at_time" value="09:00" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <label class="flex items-center space-x-2"><input type="checkbox" name="active" value="1" checked class="accent-orange-500"><span class="text-gray-600 text-sm">Active</span></label>
            <button type="submit" class="w-full py-2.5 text-white rounded-xl text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-save mr-1"></i>Add Schedule</button>
        </form>
    </div>
</div>
<script>
document.getElementById('timing').addEventListener('change',function(){
    document.getElementById('days-field').classList.toggle('hidden',this.value==='immediate'||this.value==='on_day');
});
</script>
@endsection
