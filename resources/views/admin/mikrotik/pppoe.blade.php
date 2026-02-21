@extends('layouts.admin')
@section('title','PPPoE — ' . $router->name)
@section('page-title','PPPoE Management')
@section('page-subtitle', $router->name . ' · ' . count($active) . ' active connections')
@section('content')
@include('admin.mikrotik._nav')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <!-- Active PPPoE -->
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                <h3 class="text-gray-800 font-bold text-sm">Active PPPoE Sessions ({{ count($active) }})</h3>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead style="background:#f8fafc">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Username</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">IP Address</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">MAC</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Uptime</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">↓ / ↑</th>
                        <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($active as $c)
                    <tr class="hover:bg-orange-50/30">
                        <td class="px-4 py-2.5 text-blue-700 font-mono text-xs font-bold">{{ $c['name'] ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-gray-700 font-mono text-xs">{{ $c['address'] ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-gray-500 font-mono text-xs">{{ $c['caller-id'] ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-gray-600 text-xs">{{ $c['uptime'] ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-xs"><span class="text-green-600">↓{{ $c['rx'] ?? '0' }}</span> / <span class="text-blue-500">↑{{ $c['tx'] ?? '0' }}</span></td>
                        <td class="px-4 py-2.5 text-right">
                            <form action="{{ route('admin.mikrotik.pppoe.disconnect', $router->id) }}" method="POST" class="inline">
                                @csrf <input type="hidden" name="username" value="{{ $c['name'] ?? '' }}">
                                <button class="text-red-500 hover:text-red-700 text-xs font-semibold" onclick="return confirm('Disconnect {{ $c['name'] ?? '' }}?')"><i class="fas fa-times-circle mr-1"></i>Disconnect</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400"><i class="fas fa-plug text-3xl mb-2 block text-gray-200"></i>No active PPPoE connections</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Secret + Profiles -->
    <div class="space-y-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <h3 class="text-gray-800 font-bold text-sm mb-3"><i class="fas fa-user-plus text-orange-500 mr-1"></i>Add PPPoE Secret</h3>
            <form action="{{ route('admin.mikrotik.pppoe.add', $router->id) }}" method="POST" class="space-y-3">
                @csrf
                <div><label class="block text-xs font-semibold text-gray-600 mb-1">Username</label><input name="name" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-orange-400 focus:outline-none" required></div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1">Password</label><input name="password" type="password" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-orange-400 focus:outline-none" required></div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1">Profile</label>
                    <select name="profile" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-orange-400 focus:outline-none">
                        <option value="default">default</option>
                        @foreach($profiles as $p)<option value="{{ $p['name'] ?? '' }}">{{ $p['name'] ?? '' }}</option>@endforeach
                    </select>
                </div>
                <button type="submit" class="w-full text-white py-2 rounded-lg text-xs font-bold" style="background:linear-gradient(90deg,#f97316,#ea580c)">Add Secret to MikroTik</button>
            </form>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <h3 class="text-gray-800 font-bold text-sm mb-3">PPPoE Profiles ({{ count($profiles) }})</h3>
            <div class="space-y-1.5 overflow-y-auto" style="max-height:200px">
                @forelse($profiles as $p)
                <div class="bg-gray-50 rounded-lg px-3 py-2">
                    <p class="text-gray-800 text-xs font-semibold">{{ $p['name'] ?? '-' }}</p>
                    <p class="text-gray-400 text-xs">Rate: {{ $p['rate-limit'] ?? 'unlimited' }}</p>
                </div>
                @empty
                <p class="text-gray-400 text-xs">No profiles found</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Secrets Table -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm mt-4">
    <div class="px-4 py-3 border-b border-gray-100"><h3 class="text-gray-800 font-bold text-sm">PPPoE Secrets ({{ count($secrets) }})</h3></div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead style="background:#f8fafc">
                <tr>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Name</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Profile</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Service</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">IP</th>
                    <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($secrets as $s)
                <tr class="hover:bg-orange-50/30">
                    <td class="px-4 py-2.5 text-blue-700 font-mono text-xs font-bold">{{ $s['name'] ?? '-' }}</td>
                    <td class="px-4 py-2.5 text-gray-600 text-xs">{{ $s['profile'] ?? 'default' }}</td>
                    <td class="px-4 py-2.5 text-gray-500 text-xs">{{ $s['service'] ?? 'pppoe' }}</td>
                    <td class="px-4 py-2.5 text-gray-500 font-mono text-xs">{{ $s['local-address'] ?? '-' }}</td>
                    <td class="px-4 py-2.5 text-right">
                        <form action="{{ route('admin.mikrotik.pppoe.delete', $router->id) }}" method="POST" class="inline">
                            @csrf <input type="hidden" name="name" value="{{ $s['name'] ?? '' }}">
                            <button onclick="return confirm('Delete secret?')" class="text-red-500 hover:text-red-700 text-xs"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400 text-sm">No PPPoE secrets configured</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
