<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MtaaKonnect ISP Manager')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .sidebar { background: linear-gradient(180deg, #1e3a5f 0%, #0f2744 100%); }
        .nav-item { display:flex;align-items:center;gap:8px;padding:7px 10px;border-radius:8px;font-size:12.5px;font-weight:500;transition:all 0.2s;color:#94b8d8;text-decoration:none; }
        .nav-item:hover { background:rgba(255,255,255,0.1);color:#fff; }
        .nav-item.active { background:linear-gradient(90deg,#f97316,#ea6c0a);color:#fff;box-shadow:0 2px 8px rgba(249,115,22,0.4); }
        .nav-section { font-size:10px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#4a7a9b;padding:14px 10px 3px;margin-top:2px; }
        .mk-tab { padding:6px 14px;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;transition:all 0.2s;text-decoration:none;display:inline-flex;align-items:center;gap:6px; }
        .mk-tab.active { background:linear-gradient(90deg,#f97316,#ea580c);color:#fff; }
        .mk-tab:not(.active) { background:#f1f5f9;color:#475569; }
        .mk-tab:not(.active):hover { background:#e2e8f0; }
    </style>
</head>
<body style="background:#f1f5f9;" class="font-sans">
<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="sidebar w-56 flex-shrink-0 flex flex-col overflow-y-auto shadow-xl">
        <div class="px-4 py-4 border-b border-blue-900/50">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:linear-gradient(135deg,#f97316,#ea580c)">
                    <i class="fas fa-wifi text-white text-xs"></i>
                </div>
                <div>
                    <p class="text-white font-black text-sm leading-none">MtaaKonnect</p>
                    <p class="text-blue-300 text-xs">ISP Manager</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-2 py-1 space-y-0">

            <div class="nav-section">Overview</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large w-4 text-center"></i><span>Dashboard</span>
            </a>
            <a href="{{ route('admin.sessions.live') }}" class="nav-item {{ request()->routeIs('admin.sessions*') ? 'active' : '' }}">
                <i class="fas fa-satellite-dish w-4 text-center"></i><span>Live Sessions</span>
                <span class="ml-auto text-xs bg-green-500 text-white px-1.5 py-0.5 rounded-full">LIVE</span>
            </a>

            <div class="nav-section">MikroTik</div>
            <a href="{{ route('admin.mikrotik.select') }}" class="nav-item {{ request()->routeIs('admin.mikrotik*') ? 'active' : '' }}">
                <i class="fas fa-router w-4 text-center"></i><span>MikroTik Panel</span>
                <span class="ml-auto text-xs bg-orange-500 text-white px-1.5 py-0.5 rounded-full">LIVE</span>
            </a>
            <a href="{{ route('admin.routers.index') }}" class="nav-item {{ request()->routeIs('admin.routers*') ? 'active' : '' }}">
                <i class="fas fa-network-wired w-4 text-center"></i><span>Manage Routers</span>
            </a>

            <div class="nav-section">Network</div>
            <a href="{{ route('admin.nas.index') }}" class="nav-item {{ request()->routeIs('admin.nas*') ? 'active' : '' }}">
                <i class="fas fa-server w-4 text-center"></i><span>NAS Servers</span>
            </a>
            <a href="{{ route('admin.tr069.index') }}" class="nav-item {{ request()->routeIs('admin.tr069*') ? 'active' : '' }}">
                <i class="fas fa-broadcast-tower w-4 text-center"></i><span>TR-069 Devices</span>
            </a>

            <div class="nav-section">Plans</div>
            <a href="{{ route('admin.plans.index') }}" class="nav-item {{ request()->routeIs('admin.plans*') ? 'active' : '' }}">
                <i class="fas fa-plug w-4 text-center"></i><span>PPPoE Plans</span>
            </a>
            <a href="{{ route('admin.hotspot.index') }}" class="nav-item {{ request()->routeIs('admin.hotspot*') ? 'active' : '' }}">
                <i class="fas fa-wifi w-4 text-center"></i><span>Hotspot Plans</span>
            </a>

            <div class="nav-section">Clients</div>
            <a href="{{ route('admin.clients.index') }}" class="nav-item {{ request()->routeIs('admin.clients*') ? 'active' : '' }}">
                <i class="fas fa-users w-4 text-center"></i><span>All Clients</span>
            </a>
            <a href="{{ route('admin.transactions.index') }}" class="nav-item {{ request()->routeIs('admin.transactions*') ? 'active' : '' }}">
                <i class="fas fa-money-bill-wave w-4 text-center"></i><span>Transactions</span>
            </a>

            <div class="nav-section">Communication</div>
            <a href="{{ route('admin.notifications.index') }}" class="nav-item {{ request()->routeIs('admin.notifications*') ? 'active' : '' }}">
                <i class="fas fa-bell w-4 text-center"></i><span>Notifications</span>
            </a>
            <a href="{{ route('admin.support.index') }}" class="nav-item {{ request()->routeIs('admin.support*') ? 'active' : '' }}">
                <i class="fas fa-headset w-4 text-center"></i><span>Support Tickets</span>
            </a>

            <div class="nav-section">Administration</div>
            <a href="{{ route('admin.operators.index') }}" class="nav-item {{ request()->routeIs('admin.operators*') ? 'active' : '' }}">
                <i class="fas fa-user-shield w-4 text-center"></i><span>Operators</span>
            </a>
            <a href="{{ route('admin.resellers.index') }}" class="nav-item {{ request()->routeIs('admin.resellers*') ? 'active' : '' }}">
                <i class="fas fa-building w-4 text-center"></i><span>Resellers</span>
            </a>
            <a href="{{ route('admin.services.index') }}" class="nav-item {{ request()->routeIs('admin.services*') ? 'active' : '' }}">
                <i class="fas fa-cogs w-4 text-center"></i><span>Services</span>
            </a>

            <!-- ⚙️ SETTINGS — clearly visible at the bottom of admin section -->
            <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}" style="margin-top:4px;border:1px solid rgba(249,115,22,0.3)">
                <i class="fas fa-sliders-h w-4 text-center text-orange-400"></i>
                <span>Settings</span>
                <span class="ml-auto text-orange-400"><i class="fas fa-chevron-right text-xs"></i></span>
            </a>

            <div class="mt-3 pt-3 border-t border-blue-900/50">
                <a href="{{ route('home') }}" class="nav-item">
                    <i class="fas fa-globe w-4 text-center"></i><span>View Website</span>
                </a>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-item w-full text-left hover:!bg-red-700 hover:!text-white">
                        <i class="fas fa-sign-out-alt w-4 text-center"></i><span>Logout</span>
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white border-b border-gray-200 px-5 py-3 flex justify-between items-center flex-shrink-0 shadow-sm">
            <div>
                <h1 class="text-gray-800 font-bold text-sm">@yield('page-title','Dashboard')</h1>
                <p class="text-gray-400 text-xs">@yield('page-subtitle','MtaaKonnect ISP Management')</p>
            </div>
            <div class="flex items-center space-x-3">
                <!-- Settings shortcut in header -->
                <a href="{{ route('admin.settings.index') }}" title="System Settings" class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-orange-100 text-gray-500 hover:text-orange-600 transition-colors {{ request()->routeIs('admin.settings*') ? 'bg-orange-100 text-orange-600' : '' }}">
                    <i class="fas fa-cog text-sm {{ request()->routeIs('admin.settings*') ? 'animate-spin' : '' }}"></i>
                </a>
                <div class="flex items-center space-x-1.5 bg-green-50 border border-green-200 px-2.5 py-1 rounded-full">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    <span class="text-green-700 text-xs font-semibold">Online</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-black" style="background:linear-gradient(135deg,#f97316,#ea580c)">
                        {{ strtoupper(substr(session('admin_user','A'),0,1)) }}
                    </div>
                    <div>
                        <p class="text-gray-700 text-xs font-semibold">{{ session('admin_user','Admin') }}</p>
                        <p class="text-gray-400 text-xs capitalize">{{ session('admin_role','operator') }}</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4">
            @if(session('success'))
                <div class="mb-3 bg-green-50 border border-green-300 text-green-800 px-4 py-2.5 rounded-xl flex items-center space-x-2 text-sm">
                    <i class="fas fa-check-circle text-green-500"></i>
                    <span>{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="ml-auto text-green-400 hover:text-green-600"><i class="fas fa-times"></i></button>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-3 bg-red-50 border border-red-300 text-red-800 px-4 py-2.5 rounded-xl flex items-center space-x-2 text-sm">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    <span>{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()" class="ml-auto text-red-400"><i class="fas fa-times"></i></button>
                </div>
            @endif
            @if($errors->any())
                <div class="mb-3 bg-red-50 border border-red-300 text-red-800 px-4 py-2.5 rounded-xl text-sm">
                    <p class="font-semibold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i>Please fix the following:</p>
                    <ul class="list-disc ml-4 space-y-0.5">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
