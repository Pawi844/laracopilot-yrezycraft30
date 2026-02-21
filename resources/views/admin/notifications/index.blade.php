@extends('layouts.admin')
@section('title','Notifications')
@section('page-title','Notifications & Messaging')
@section('content')
<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-5">
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 text-center">
        <i class="fas fa-sms text-green-400 text-xl mb-1"></i>
        <p class="text-white font-bold text-lg">{{ number_format($stats['sms']) }}</p>
        <p class="text-slate-500 text-xs">SMS Total</p>
    </div>
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 text-center">
        <i class="fas fa-sms text-green-300 text-xl mb-1"></i>
        <p class="text-white font-bold text-lg">{{ number_format($stats['sms_today']) }}</p>
        <p class="text-slate-500 text-xs">SMS Today</p>
    </div>
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 text-center">
        <i class="fab fa-whatsapp text-green-500 text-xl mb-1"></i>
        <p class="text-white font-bold text-lg">{{ number_format($stats['whatsapp']) }}</p>
        <p class="text-slate-500 text-xs">WhatsApp</p>
    </div>
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 text-center">
        <i class="fas fa-envelope text-blue-400 text-xl mb-1"></i>
        <p class="text-white font-bold text-lg">{{ number_format($stats['email']) }}</p>
        <p class="text-slate-500 text-xs">Emails</p>
    </div>
    <div class="bg-slate-800 border border-red-900/50 rounded-xl p-4 text-center">
        <i class="fas fa-times-circle text-red-400 text-xl mb-1"></i>
        <p class="text-white font-bold text-lg">{{ number_format($stats['failed']) }}</p>
        <p class="text-slate-500 text-xs">Failed</p>
    </div>
</div>

<!-- Offline Clients Alert -->
@if($offlineClients->count())
<div class="bg-red-900/30 border border-red-700 rounded-xl p-4 mb-5">
    <div class="flex items-center space-x-2 mb-3">
        <i class="fas fa-exclamation-triangle text-red-400"></i>
        <h3 class="text-red-300 font-bold text-sm">{{ $offlineClients->count() }} Clients Offline for 12+ Hours</h3>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
        @foreach($offlineClients->take(8) as $c)
        <div class="bg-slate-800 rounded-lg px-3 py-2 flex justify-between items-center">
            <div><p class="text-white text-xs font-semibold">{{ $c->username }}</p><p class="text-red-400 text-xs">{{ $c->offline_hours }}h offline</p></div>
        </div>
        @endforeach
    </div>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    <!-- Send SMS -->
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-5">
        <h3 class="text-white font-bold text-sm mb-4"><i class="fas fa-sms text-green-400 mr-2"></i>Send SMS</h3>
        <form action="{{ route('admin.notifications.sms') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs text-slate-400 mb-1">Client (Optional)</label>
                <select name="client_id" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="">Select client or enter number below</option>
                    @foreach($clients as $c)
                    <option value="{{ $c->id }}">{{ $c->username }} — {{ $c->phone }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1">Phone Number *</label>
                <input type="text" name="recipient" placeholder="+254712345678" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-sky-500 focus:outline-none" required>
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1">Message *</label>
                <textarea name="message" rows="4" placeholder="Type SMS message..." class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-sky-500 focus:outline-none" required></textarea>
            </div>
            <button type="submit" class="w-full bg-green-700 text-white py-2 rounded-lg text-sm font-semibold hover:bg-green-600"><i class="fas fa-sms mr-1"></i>Send SMS</button>
        </form>
    </div>

    <!-- Send WhatsApp -->
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-5">
        <h3 class="text-white font-bold text-sm mb-4"><i class="fab fa-whatsapp text-green-500 mr-2"></i>Send WhatsApp</h3>
        <form action="{{ route('admin.notifications.whatsapp') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs text-slate-400 mb-1">Client (Optional)</label>
                <select name="client_id" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="">Select client</option>
                    @foreach($clients as $c)
                    <option value="{{ $c->id }}">{{ $c->username }} — {{ $c->phone }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1">WhatsApp Number *</label>
                <input type="text" name="recipient" placeholder="+254712345678" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-sky-500 focus:outline-none" required>
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1">Message *</label>
                <textarea name="message" rows="4" placeholder="Type WhatsApp message..." class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-sky-500 focus:outline-none" required></textarea>
            </div>
            <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg text-sm font-semibold hover:bg-green-700"><i class="fab fa-whatsapp mr-1"></i>Send WhatsApp</button>
        </form>
    </div>

    <!-- Send Email -->
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-5">
        <h3 class="text-white font-bold text-sm mb-4"><i class="fas fa-envelope text-blue-400 mr-2"></i>Send Email</h3>
        <form action="{{ route('admin.notifications.email') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs text-slate-400 mb-1">Client (Optional)</label>
                <select name="client_id" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="">Select client</option>
                    @foreach($clients as $c)
                    <option value="{{ $c->id }}">{{ $c->username }} — {{ $c->email }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1">Email Address *</label>
                <input type="email" name="recipient" placeholder="client@example.com" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-sky-500 focus:outline-none" required>
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1">Subject *</label>
                <input type="text" name="subject" placeholder="Subject" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-sky-500 focus:outline-none" required>
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1">Message *</label>
                <textarea name="message" rows="2" placeholder="Email body..." class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-sky-500 focus:outline-none" required></textarea>
            </div>
            <button type="submit" class="w-full bg-blue-700 text-white py-2 rounded-lg text-sm font-semibold hover:bg-blue-600"><i class="fas fa-envelope mr-1"></i>Send Email</button>
        </form>
    </div>
</div>

<!-- Broadcast -->
<div class="bg-slate-800 border border-yellow-900/50 rounded-xl p-5 mb-5">
    <h3 class="text-white font-bold text-sm mb-4"><i class="fas fa-bullhorn text-yellow-400 mr-2"></i>Broadcast to Clients</h3>
    <form action="{{ route('admin.notifications.broadcast') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs text-slate-400 mb-1">Target Clients</label>
                <select name="target" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="all">All Clients</option>
                    <option value="active">Active Clients Only</option>
                    <option value="expired">Expired Clients</option>
                    <option value="suspended">Suspended Clients</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-2">Channels</label>
                <div class="flex space-x-3">
                    <label class="flex items-center space-x-1 text-slate-300 text-xs"><input type="checkbox" name="channels[]" value="sms" class="rounded"><span>SMS</span></label>
                    <label class="flex items-center space-x-1 text-slate-300 text-xs"><input type="checkbox" name="channels[]" value="whatsapp" class="rounded"><span>WhatsApp</span></label>
                    <label class="flex items-center space-x-1 text-slate-300 text-xs"><input type="checkbox" name="channels[]" value="email" class="rounded"><span>Email</span></label>
                </div>
            </div>
            <div class="flex items-end">
                <button type="submit" onclick="return confirm('Broadcast to all selected clients?')" class="w-full bg-yellow-700 text-white py-2 rounded-lg text-sm font-semibold hover:bg-yellow-600"><i class="fas fa-bullhorn mr-1"></i>Send Broadcast</button>
            </div>
        </div>
        <div class="mt-3">
            <label class="block text-xs text-slate-400 mb-1">Broadcast Message *</label>
            <textarea name="message" rows="2" placeholder="Message to broadcast to all selected clients..." class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none" required></textarea>
        </div>
    </form>
</div>

<!-- Log -->
<div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
    <div class="px-5 py-3 border-b border-slate-700"><h3 class="text-white font-bold text-sm">Notification Log</h3></div>
    <table class="w-full text-sm">
        <thead class="bg-slate-900"><tr>
            <th class="px-4 py-2 text-left text-xs text-slate-500">Channel</th>
            <th class="px-4 py-2 text-left text-xs text-slate-500">Recipient</th>
            <th class="px-4 py-2 text-left text-xs text-slate-500">Message</th>
            <th class="px-4 py-2 text-left text-xs text-slate-500">Status</th>
            <th class="px-4 py-2 text-left text-xs text-slate-500">Sent</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-700">
            @forelse($logs as $log)
            <tr class="hover:bg-slate-700/40">
                <td class="px-4 py-2">
                    <span class="text-xs px-2 py-0.5 rounded
                        {{ $log->channel === 'sms' ? 'bg-green-900 text-green-300' : '' }}
                        {{ $log->channel === 'whatsapp' ? 'bg-green-800 text-green-200' : '' }}
                        {{ $log->channel === 'email' ? 'bg-blue-900 text-blue-300' : '' }}">
                        {{ strtoupper($log->channel) }}
                    </span>
                </td>
                <td class="px-4 py-2 text-slate-300 text-xs font-mono">{{ $log->recipient }}</td>
                <td class="px-4 py-2 text-slate-400 text-xs">{{ Str::limit($log->message, 50) }}</td>
                <td class="px-4 py-2">
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $log->status === 'sent' ? 'bg-green-900 text-green-300' : 'bg-red-900 text-red-300' }}">{{ ucfirst($log->status) }}</span>
                </td>
                <td class="px-4 py-2 text-slate-500 text-xs">{{ $log->created_at->diffForHumans() }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">No notifications sent yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-slate-700">{{ $logs->links() }}</div>
</div>
@endsection
