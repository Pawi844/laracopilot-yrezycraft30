<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title','Admin') — {{ \App\Models\SystemSetting::get('general','company_name','ISP Admin') }}</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body{font-family:'Inter',system-ui,sans-serif;background:#f8fafc;}
#sidebar{width:220px;min-height:100vh;background:linear-gradient(160deg,#1e3a5f 0%,#0f2744 60%,#1a1a2e 100%);position:fixed;top:0;left:0;z-index:40;transition:transform .25s ease;overflow-y:auto;display:flex;flex-direction:column;}
#main-wrap{margin-left:220px;min-height:100vh;transition:margin-left .25s ease;}
@media(max-width:1023px){#sidebar{transform:translateX(-100%);}#sidebar.open{transform:translateX(0);}#main-wrap{margin-left:0;}}
.nav-link{display:flex;align-items:center;gap:8px;padding:7px 12px;border-radius:7px;font-size:12.5px;font-weight:500;color:rgba(255,255,255,.7);text-decoration:none;transition:all .15s;margin:1px 6px;}
.nav-link:hover{background:rgba(255,255,255,.1);color:#fff;}
.nav-link.active{background:linear-gradient(90deg,#f97316,#ea580c);color:#fff;box-shadow:0 2px 8px rgba(249,115,22,.3);}
.nav-link i{width:15px;text-align:center;font-size:12px;flex-shrink:0;}
.nav-sec{font-size:9.5px;font-weight:700;color:rgba(255,255,255,.3);padding:10px 18px 3px;text-transform:uppercase;letter-spacing:.08em;}
#overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:39;}
#overlay.open{display:block;}
::-webkit-scrollbar{width:3px;height:3px;}::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:2px;}
</style>
</head>
<body>
<div id="overlay" onclick="closeSidebar()"></div>

<!-- SIDEBAR -->
<aside id="sidebar">
    <!-- Logo -->
    <div class="px-4 py-4 border-b border-white/10 flex-shrink-0">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div class="w-7 h-7 rounded-lg bg-orange-500 flex items-center justify-center"><i class="fas fa-wifi text-white text-xs"></i></div>
                <div>
                    <p class="text-white font-black text-xs leading-tight">{{ \App\Models\SystemSetting::get('general','company_name','ISP') }}</p>
                    <p class="text-blue-300 text-[10px]">Admin Panel</p>
                </div>
            </div>
            <button onclick="closeSidebar()" class="lg:hidden text-white/50 hover:text-white text-sm"><i class="fas fa-times"></i></button>
        </div>
    </div>
    <!-- Search -->
    <div class="px-3 py-2 flex-shrink-0">
        <form action="{{ route('admin.clients.index') }}" method="GET">
            <div class="relative">
                <i class="fas fa-search absolute left-2 top-1/2 -translate-y-1/2 text-white/30 text-xs"></i>
                <input type="text" name="q" placeholder="Search clients..." class="w-full bg-white/10 border border-white/15 rounded-lg pl-6 pr-2 py-1.5 text-xs text-white placeholder-white/35 focus:outline-none focus:bg-white/15">
            </div>
        </form>
    </div>
    <!-- Nav -->
    <nav class="flex-1 overflow-y-auto pb-4">
        <p class="nav-sec">Main</p>
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard')?'active':'' }}"><i class="fas fa-th-large"></i>Dashboard</a>

        <p class="nav-sec">Subscribers</p>
        <a href="{{ route('admin.clients.index') }}" class="nav-link {{ request()->routeIs('admin.clients.*')?'active':'' }}"><i class="fas fa-users"></i>Clients</a>
        <a href="{{ route('admin.plans.index') }}" class="nav-link {{ request()->routeIs('admin.plans.*')?'active':'' }}"><i class="fas fa-tags"></i>Plans</a>
        <a href="{{ route('admin.transactions.index') }}" class="nav-link {{ request()->routeIs('admin.transactions.*')?'active':'' }}"><i class="fas fa-money-bill-wave"></i>Transactions</a>

        <p class="nav-sec">Network</p>
        <a href="{{ route('admin.nas.index') }}" class="nav-link {{ request()->routeIs('admin.nas.*')?'active':'' }}"><i class="fas fa-server"></i>NAS / RADIUS</a>
        <a href="{{ route('admin.routers.index') }}" class="nav-link {{ request()->routeIs('admin.routers.*')?'active':'' }}"><i class="fas fa-router"></i>Routers</a>
        <a href="{{ route('admin.mikrotik.select') }}" class="nav-link {{ request()->routeIs('admin.mikrotik.*')?'active':'' }}"><i class="fas fa-network-wired"></i>MikroTik</a>
        <a href="{{ route('admin.sessions.index') }}" class="nav-link {{ request()->routeIs('admin.sessions.*')?'active':'' }}"><i class="fas fa-satellite-dish"></i>Sessions</a>

        <p class="nav-sec">Fiber / OLT</p>
        <a href="{{ route('admin.fat.index') }}" class="nav-link {{ request()->routeIs('admin.fat.*')?'active':'' }}"><i class="fas fa-project-diagram"></i>FAT Nodes</a>
        <a href="{{ route('admin.olt.index') }}" class="nav-link {{ request()->routeIs('admin.olt.*')?'active':'' }}"><i class="fas fa-server"></i>OLT Devices</a>
        <a href="{{ route('admin.tr069.index') }}" class="nav-link {{ request()->routeIs('admin.tr069.*')?'active':'' }}"><i class="fas fa-broadcast-tower"></i>TR-069 ONU</a>
        <a href="{{ route('admin.hotspot.index') }}" class="nav-link {{ request()->routeIs('admin.hotspot.*')?'active':'' }}"><i class="fas fa-wifi"></i>Hotspot</a>

        <p class="nav-sec">Support</p>
        <a href="{{ route('admin.support.index') }}" class="nav-link {{ request()->routeIs('admin.support.*')?'active':'' }}">
            <i class="fas fa-ticket-alt"></i>Tickets
            @php try { $ot=\App\Models\SupportTicket::where('status','open')->count(); } catch(\Exception $e){$ot=0;} @endphp
            @if($ot)<span class="ml-auto bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $ot }}</span>@endif
        </a>
        <a href="{{ route('admin.callcentre.index') }}" class="nav-link {{ request()->routeIs('admin.callcentre.*')?'active':'' }}"><i class="fas fa-headset"></i>Call Centre</a>
        <a href="{{ route('admin.notifications.index') }}" class="nav-link {{ request()->routeIs('admin.notifications.*')?'active':'' }}"><i class="fas fa-bell"></i>Notifications</a>

        <p class="nav-sec">Business</p>
        <a href="{{ route('admin.resellers.index') }}" class="nav-link {{ request()->routeIs('admin.resellers.*')?'active':'' }}"><i class="fas fa-store"></i>Resellers</a>
        <a href="{{ route('admin.operators.index') }}" class="nav-link {{ request()->routeIs('admin.operators.*')?'active':'' }}"><i class="fas fa-user-tie"></i>Operators</a>
        <a href="{{ route('admin.services.index') }}" class="nav-link {{ request()->routeIs('admin.services.*')?'active':'' }}"><i class="fas fa-concierge-bell"></i>Services</a>

        <p class="nav-sec">Settings</p>
        <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*')?'active':'' }}"><i class="fas fa-cog"></i>Settings</a>
        <a href="{{ route('admin.callcentre.settings') }}" class="nav-link {{ request()->is('admin/callcentre/settings')?'active':'' }}"><i class="fas fa-phone-alt"></i>3CX / VoIP</a>

        <div class="mx-2 mt-3">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center space-x-2 px-3 py-2 rounded-lg bg-white/10 hover:bg-red-500/30 text-white/60 hover:text-white text-xs transition-all">
                    <i class="fas fa-sign-out-alt text-xs"></i><span>Logout</span>
                </button>
            </form>
        </div>
    </nav>
</aside>

<!-- MAIN -->
<div id="main-wrap">
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
        <div class="flex items-center h-13 px-3 sm:px-4 space-x-3">
            <button onclick="openSidebar()" class="lg:hidden w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-600 flex-shrink-0">
                <i class="fas fa-bars text-sm"></i>
            </button>
            <div class="flex-1 min-w-0">
                <h1 class="text-gray-800 font-black text-sm sm:text-base truncate">@yield('page-title','Dashboard')</h1>
                <p class="text-gray-400 text-xs hidden sm:block truncate">@yield('page-subtitle','')</p>
            </div>
            <div class="flex items-center space-x-2">
                <!-- Desktop global search -->
                <form action="{{ route('admin.clients.index') }}" method="GET" class="hidden md:block">
                    <div class="relative">
                        <i class="fas fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search..." class="w-44 lg:w-56 border border-gray-200 rounded-lg pl-7 pr-3 py-1.5 text-xs focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    </div>
                </form>
                @if(isset($ot) && $ot)
                <a href="{{ route('admin.support.index') }}" class="relative w-8 h-8 flex items-center justify-center rounded-lg hover:bg-orange-50 text-gray-500">
                    <i class="fas fa-bell text-sm"></i>
                    <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">{{ min($ot,9) }}</span>
                </a>
                @endif
                <div class="flex items-center space-x-1.5 bg-gray-50 rounded-lg px-2 py-1.5">
                    <div class="w-5 h-5 rounded-full bg-orange-500 flex items-center justify-center text-white text-xs font-black">{{ strtoupper(substr(session('admin_user','A'),0,1)) }}</div>
                    <span class="text-gray-700 text-xs font-semibold hidden sm:inline">{{ session('admin_user','Admin') }}</span>
                </div>
            </div>
        </div>
    </header>

    <div class="px-3 sm:px-4 pt-3">
        @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-2.5 rounded-xl text-sm flex items-center space-x-2 mb-3"><i class="fas fa-check-circle"></i><span>{{ session('success') }}</span></div>@endif
        @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-2.5 rounded-xl text-sm flex items-center space-x-2 mb-3"><i class="fas fa-times-circle"></i><span>{{ session('error') }}</span></div>@endif
        @if($errors->any())<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-2.5 rounded-xl text-sm mb-3"><i class="fas fa-exclamation-circle mr-1"></i>{{ $errors->first() }}</div>@endif
    </div>

    <main class="px-3 sm:px-4 py-3 sm:py-4 pb-10">
        @yield('content')
    </main>
</div>
<script>
function openSidebar(){document.getElementById('sidebar').classList.add('open');document.getElementById('overlay').classList.add('open');document.body.style.overflow='hidden';}
function closeSidebar(){document.getElementById('sidebar').classList.remove('open');document.getElementById('overlay').classList.remove('open');document.body.style.overflow='';}
</script>
</body>
</html>
