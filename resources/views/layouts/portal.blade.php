<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>@yield('title','My Account') — {{ \App\Models\SystemSetting::get('general','company_name','ISP') }}</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
.portal-nav a { display:flex;align-items:center;gap:8px;padding:8px 14px;border-radius:8px;font-size:13px;font-weight:500;color:#64748b;text-decoration:none;transition:all 0.2s; }
.portal-nav a:hover { background:#fff7ed;color:#ea580c; }
.portal-nav a.active { background:linear-gradient(90deg,#f97316,#ea580c);color:#fff;box-shadow:0 2px 8px rgba(249,115,22,0.3); }
</style>
</head>
<body class="bg-gray-50 font-sans">
<div class="min-h-screen">
    <header class="border-b border-gray-200 bg-white shadow-sm">
        <div class="max-w-5xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:linear-gradient(135deg,#f97316,#ea580c)">
                    <i class="fas fa-wifi text-white text-xs"></i>
                </div>
                <p class="font-black text-gray-800">{{ \App\Models\SystemSetting::get('general','company_name','ISP') }}</p>
            </div>
            <div class="flex items-center space-x-3">
                @php $clientName = \App\Models\IspClient::find(session('portal_client_id')); @endphp
                <span class="text-gray-600 text-sm">{{ $clientName?->first_name }}</span>
                <form action="{{ route('portal.logout') }}" method="POST">
                    @csrf
                    <button class="text-gray-400 hover:text-red-500 text-xs"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </div>
        </div>
    </header>
    <div class="max-w-5xl mx-auto px-4 py-5">
        @if(session('success'))<div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-2.5 rounded-xl text-sm"><i class="fas fa-check-circle mr-1"></i>{{ session('success') }}</div>@endif
        @if(session('error'))<div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-2.5 rounded-xl text-sm"><i class="fas fa-times-circle mr-1"></i>{{ session('error') }}</div>@endif
        @if($errors->any())<div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-2.5 rounded-xl text-sm">{{ $errors->first() }}</div>@endif
        <div class="flex flex-wrap gap-1 portal-nav mb-5 bg-white rounded-xl border border-gray-100 shadow-sm p-2">
            <a href="{{ route('portal.dashboard') }}" class="{{ request()->routeIs('portal.dashboard') ? 'active' : '' }}"><i class="fas fa-th-large"></i>Dashboard</a>
            <a href="{{ route('portal.bills') }}" class="{{ request()->routeIs('portal.bills') ? 'active' : '' }}"><i class="fas fa-file-invoice"></i>Bills</a>
            <a href="{{ route('portal.utilization') }}" class="{{ request()->routeIs('portal.utilization') ? 'active' : '' }}"><i class="fas fa-chart-line"></i>Usage</a>
            <a href="{{ route('portal.topup') }}" class="{{ request()->routeIs('portal.topup') ? 'active' : '' }}"><i class="fas fa-money-bill-wave"></i>Top Up</a>
            <a href="{{ route('portal.change_plan') }}" class="{{ request()->routeIs('portal.change_plan') ? 'active' : '' }}"><i class="fas fa-exchange-alt"></i>Change Plan</a>
            <a href="{{ route('portal.devices') }}" class="{{ request()->routeIs('portal.devices') ? 'active' : '' }}"><i class="fas fa-router"></i>Devices</a>
            <a href="{{ route('portal.profile') }}" class="{{ request()->routeIs('portal.profile') ? 'active' : '' }}"><i class="fas fa-user-cog"></i>Profile</a>
        </div>
        @yield('content')
    </div>
</div>
</body>
</html>
