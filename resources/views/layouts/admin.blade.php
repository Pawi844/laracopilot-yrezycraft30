<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MtaaKonnect ISP Manager')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .sidebar-link { @apply flex items-center space-x-2 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200; }
        .sidebar-link.active { @apply bg-sky-600 text-white; }
        .sidebar-link:not(.active) { @apply text-slate-300 hover:bg-slate-700 hover:text-white; }
        .sidebar-group { @apply text-xs font-bold text-slate-500 uppercase tracking-widest px-3 mt-5 mb-1; }
    </style>
</head>
<body class="bg-slate-900 font-sans">
<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-60 bg-slate-800 flex-shrink-0 flex flex-col overflow-y-auto border-r border-slate-700">
        <div class="px-4 py-5 border-b border-slate-700 flex items-center space-x-3">
            <div class="w-9 h-9 bg-sky-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-wifi text-white text-sm"></i>
            </div>
            <div>
                <p class="text-white font-black text-sm">MtaaKonnect</p>
                <p class="text-slate-400 text-xs">ISP Manager v2</p>
            </div>
        </div>

        <nav class="flex-1 p-3 space-y-0.5">
            <p class="sidebar-group">Overview</p>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt w-4"></i><span>Dashboard</span>
            </a>
            <a href="{{ route('admin.sessions.live') }}" class="sidebar-link {{ request()->routeIs('admin.sessions*') ? 'active' : '' }}">
                <i class="fas fa-satellite-dish w-4"></i><span>Live Sessions</span>
                <span class="ml-auto bg-green-500 text-white text-xs px-1.5 py-0.5 rounded-full">LIVE</span>
            </a>

            <p class="sidebar-group">Network</p>
            <a href="{{ route('admin.nas.index') }}" class="sidebar-link {{ request()->routeIs('admin.nas*') ? 'active' : '' }}">
                <i class="fas fa-server w-4"></i><span>NAS Servers</span>
            </a>
            <a href="{{ route('admin.routers.index') }}" class="sidebar-link {{ request()->routeIs('admin.routers*') ? 'active' : '' }}">
                <i class="fas fa-network-wired w-4"></i><span>MikroTik Routers</span>
            </a>
            <a href="{{ route('admin.tr069.index') }}" class="sidebar-link {{ request()->routeIs('admin.tr069*') ? 'active' : '' }}">
                <i class="fas fa-router w-4"></i><span>TR-069 Devices</span>
            </a>

            <p class="sidebar-group">Plans</p>
            <a href="{{ route('admin.plans.index') }}" class="sidebar-link {{ request()->routeIs('admin.plans*') ? 'active' : '' }}">
                <i class="fas fa-plug w-4"></i><span>PPPoE Plans</span>
            </a>
            <a href="{{ route('admin.hotspot.index') }}" class="sidebar-link {{ request()->routeIs('admin.hotspot*') ? 'active' : '' }}">
                <i class="fas fa-hotdog w-4"></i><span>Hotspot Plans</span>
            </a>

            <p class="sidebar-group">Clients</p>
            <a href="{{ route('admin.clients.index') }}" class="sidebar-link {{ request()->routeIs('admin.clients*') ? 'active' : '' }}">
                <i class="fas fa-users w-4"></i><span>All Clients</span>
            </a>
            <a href="{{ route('admin.transactions.index') }}" class="sidebar-link {{ request()->routeIs('admin.transactions*') ? 'active' : '' }}">
                <i class="fas fa-money-bill-wave w-4"></i><span>Transactions</span>
            </a>

            <p class="sidebar-group">Communication</p>
            <a href="{{ route('admin.notifications.index') }}" class="sidebar-link {{ request()->routeIs('admin.notifications*') ? 'active' : '' }}">
                <i class="fas fa-bell w-4"></i><span>Notifications</span>
            </a>
            <a href="{{ route('admin.support.index') }}" class="sidebar-link {{ request()->routeIs('admin.support*') ? 'active' : '' }}">
                <i class="fas fa-headset w-4"></i><span>Support Tickets</span>
            </a>

            @if(in_array(session('admin_role'), ['superadmin','admin']))
            <p class="sidebar-group">Administration</p>
            <a href="{{ route('admin.operators.index') }}" class="sidebar-link {{ request()->routeIs('admin.operators*') ? 'active' : '' }}">
                <i class="fas fa-user-shield w-4"></i><span>Operators</span>
            </a>
            <a href="{{ route('admin.resellers.index') }}" class="sidebar-link {{ request()->routeIs('admin.resellers*') ? 'active' : '' }}">
                <i class="fas fa-building w-4"></i><span>Resellers</span>
            </a>
            <a href="{{ route('admin.services.index') }}" class="sidebar-link {{ request()->routeIs('admin.services*') ? 'active' : '' }}">
                <i class="fas fa-cogs w-4"></i><span>Services</span>
            </a>
            @endif

            <div class="pt-4 border-t border-slate-700 mt-4 space-y-0.5">
                <a href="{{ route('home') }}" class="sidebar-link">
                    <i class="fas fa-globe w-4"></i><span>View Website</span>
                </a>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="sidebar-link w-full text-left hover:bg-red-700">
                        <i class="fas fa-sign-out-alt w-4"></i><span>Logout</span>
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col overflow-hidden bg-slate-900">
        <!-- Top Bar -->
        <header class="bg-slate-800 border-b border-slate-700 px-6 py-3 flex justify-between items-center flex-shrink-0">
            <div class="flex items-center space-x-3">
                <h1 class="text-white font-semibold text-sm">@yield('page-title','Dashboard')</h1>
            </div>
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2 bg-green-900/40 text-green-400 px-3 py-1 rounded-full text-xs">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    <span>System Online</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-slate-400 text-xs">{{ session('admin_user','Admin') }}</span>
                    <span class="bg-sky-600 text-white text-xs px-2 py-0.5 rounded capitalize">{{ session('admin_role','operator') }}</span>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-5">
            @if(session('success'))
                <div class="mb-4 bg-green-900/50 border border-green-600 text-green-300 px-4 py-3 rounded-lg flex items-center space-x-2 text-sm">
                    <i class="fas fa-check-circle"></i><span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-900/50 border border-red-600 text-red-300 px-4 py-3 rounded-lg flex items-center space-x-2 text-sm">
                    <i class="fas fa-exclamation-circle"></i><span>{{ session('error') }}</span>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
