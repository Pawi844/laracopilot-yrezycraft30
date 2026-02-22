<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title','Admin') — {{ \App\Models\SystemSetting::get('general','company_name','ISP Admin') }}</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*,*::before,*::after{box-sizing:border-box;}
html,body{height:100%;margin:0;padding:0;}
body{font-family:'Inter',system-ui,sans-serif;background:#f1f5f9;overflow-x:hidden;}

/* ── Sidebar ── */
#sidebar{
    position:fixed;
    top:0;left:0;
    width:220px;
    height:100vh;
    background:linear-gradient(170deg,#1e3a5f 0%,#0f2744 55%,#161630 100%);
    z-index:50;
    display:flex;
    flex-direction:column;
    transition:transform .25s cubic-bezier(.4,0,.2,1);
    will-change:transform;
}

/* Sidebar inner scroll area */
#sidebar-nav{
    flex:1;
    overflow-y:auto;
    overflow-x:hidden;
    -webkit-overflow-scrolling:touch;
    padding-bottom:16px;
}
/* Custom thin scrollbar inside sidebar */
#sidebar-nav::-webkit-scrollbar{width:3px;}
#sidebar-nav::-webkit-scrollbar-track{background:transparent;}
#sidebar-nav::-webkit-scrollbar-thumb{background:rgba(255,255,255,.2);border-radius:2px;}

/* Desktop: sidebar always visible */
@media(min-width:1024px){
    #sidebar{transform:translateX(0) !important;}
    #main-wrap{margin-left:220px;}
    #overlay{display:none !important;}
    #menu-btn{display:none;}
}
/* Mobile/Tablet: sidebar hidden off-screen by default */
@media(max-width:1023px){
    #sidebar{transform:translateX(-100%);}
    #sidebar.open{transform:translateX(0);}
    #main-wrap{margin-left:0;}
}

/* ── Main content ── */
#main-wrap{
    min-height:100vh;
    display:flex;
    flex-direction:column;
    transition:margin-left .25s cubic-bezier(.4,0,.2,1);
}

/* ── Overlay ── */
#overlay{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.55);
    z-index:49;
    backdrop-filter:blur(2px);
}
#overlay.open{display:block;}

/* ── Nav links ── */
.nav-link{
    display:flex;
    align-items:center;
    gap:9px;
    padding:7px 14px;
    border-radius:8px;
    font-size:12.5px;
    font-weight:500;
    color:rgba(255,255,255,.68);
    text-decoration:none;
    transition:background .15s,color .15s;
    margin:1px 8px;
    white-space:nowrap;
}
.nav-link:hover{background:rgba(255,255,255,.1);color:#fff;}
.nav-link.active{
    background:linear-gradient(90deg,#f97316,#ea580c);
    color:#fff;
    box-shadow:0 2px 10px rgba(249,115,22,.3);
}
.nav-link i{width:16px;text-align:center;font-size:12px;flex-shrink:0;}
.nav-sec{
    font-size:9.5px;
    font-weight:700;
    color:rgba(255,255,255,.28);
    padding:10px 22px 3px;
    text-transform:uppercase;
    letter-spacing:.09em;
    user-select:none;
}
.badge{
    margin-left:auto;
    background:#ef4444;
    color:#fff;
    font-size:10px;
    font-weight:700;
    padding:1px 6px;
    border-radius:20px;
    line-height:1.4;
    flex-shrink:0;
}

/* ── Page content scrollbar ── */
::-webkit-scrollbar{width:5px;height:5px;}
::-webkit-scrollbar-track{background:#f1f5f9;}
::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:3px;}
</style>
</head>
<body>

<!-- Overlay (mobile) -->
<div id="overlay" onclick="closeSidebar()"></div>

<!-- ═══════════════ SIDEBAR ═══════════════ -->
<aside id="sidebar">

    <!-- Logo / Brand -->
    <div style="flex-shrink:0;padding:14px 14px 10px;border-bottom:1px solid rgba(255,255,255,.08);">
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:9px;">
                <div style="width:30px;height:30px;background:linear-gradient(135deg,#f97316,#ea580c);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-wifi" style="color:#fff;font-size:13px;"></i>
                </div>
                <div style="overflow:hidden;">
                    <p style="color:#fff;font-weight:900;font-size:12.5px;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:140px;">{{ \App\Models\SystemSetting::get('general','company_name','ISP Manager') }}</p>
                    <p style="color:rgba(147,197,253,.7);font-size:10px;">Admin Panel</p>
                </div>
            </div>
            <!-- Close button — mobile only -->
            <button onclick="closeSidebar()" class="lg:hidden" style="color:rgba(255,255,255,.5);background:none;border:none;cursor:pointer;padding:4px;font-size:14px;">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Search -->
    <div style="flex-shrink:0;padding:8px 10px;">
        <form action="{{ route('admin.clients.index') }}" method="GET">
            <div style="position:relative;">
                <i class="fas fa-search" style="position:absolute;left:9px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,.3);font-size:11px;"></i>
                <input type="text" name="q" placeholder="Search clients..." style="width:100%;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:8px;padding:7px 10px 7px 28px;font-size:12px;color:#fff;outline:none;" onfocus="this.style.background='rgba(255,255,255,.13)'" onblur="this.style.background='rgba(255,255,255,.08)'">
            </div>
        </form>
    </div>

    <!-- ── Scrollable Nav ── -->
    <nav id="sidebar-nav">

        <p class="nav-sec">Main</p>
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard')?'active':'' }}">
            <i class="fas fa-th-large"></i>Dashboard
        </a>

        <p class="nav-sec">Subscribers</p>
        <a href="{{ route('admin.clients.index') }}" class="nav-link {{ request()->routeIs('admin.clients.*')?'active':'' }}">
            <i class="fas fa-users"></i>Clients
        </a>
        <a href="{{ route('admin.plans.index') }}" class="nav-link {{ request()->routeIs('admin.plans.*')?'active':'' }}">
            <i class="fas fa-tags"></i>Plans
        </a>
        <a href="{{ route('admin.transactions.index') }}" class="nav-link {{ request()->routeIs('admin.transactions.*')?'active':'' }}">
            <i class="fas fa-money-bill-wave"></i>Transactions
        </a>
        <a href="{{ route('admin.services.index') }}" class="nav-link {{ request()->routeIs('admin.services.*')?'active':'' }}">
            <i class="fas fa-concierge-bell"></i>Services
        </a>

        <p class="nav-sec">Network</p>
        <a href="{{ route('admin.nas.index') }}" class="nav-link {{ request()->routeIs('admin.nas.*')?'active':'' }}">
            <i class="fas fa-server"></i>NAS / RADIUS
        </a>
        <a href="{{ route('admin.routers.index') }}" class="nav-link {{ request()->routeIs('admin.routers.*')?'active':'' }}">
            <i class="fas fa-router"></i>Routers
        </a>
        <a href="{{ route('admin.mikrotik.select') }}" class="nav-link {{ request()->routeIs('admin.mikrotik.*')?'active':'' }}">
            <i class="fas fa-network-wired"></i>MikroTik
        </a>
        <a href="{{ route('admin.sessions.index') }}" class="nav-link {{ request()->routeIs('admin.sessions.*')?'active':'' }}">
            <i class="fas fa-satellite-dish"></i>Live Sessions
        </a>

        <p class="nav-sec">Fiber / OLT</p>
        <a href="{{ route('admin.fat.index') }}" class="nav-link {{ request()->routeIs('admin.fat.*')?'active':'' }}">
            <i class="fas fa-project-diagram"></i>FAT Nodes
        </a>
        <a href="{{ route('admin.olt.index') }}" class="nav-link {{ request()->routeIs('admin.olt.*')?'active':'' }}">
            <i class="fas fa-server"></i>OLT Devices
        </a>
        <a href="{{ route('admin.tr069.index') }}" class="nav-link {{ request()->routeIs('admin.tr069.*')?'active':'' }}">
            <i class="fas fa-broadcast-tower"></i>TR-069 ONU
        </a>

        <p class="nav-sec">Hotspot</p>
        <a href="{{ route('admin.hotspot.index') }}" class="nav-link {{ request()->routeIs('admin.hotspot.index')?'active':'' }}">
            <i class="fas fa-wifi"></i>Hotspot
        </a>
        <a href="{{ route('admin.hotspot.captive') }}" class="nav-link {{ request()->routeIs('admin.hotspot.captive')?'active':'' }}">
            <i class="fas fa-mobile-alt"></i>Captive Portal
        </a>

        <p class="nav-sec">Support</p>
        <a href="{{ route('admin.support.index') }}" class="nav-link {{ request()->routeIs('admin.support.*')?'active':'' }}">
            <i class="fas fa-ticket-alt"></i>Tickets
            @php try { $ot=\App\Models\SupportTicket::where('status','open')->count(); } catch(\Exception $e){$ot=0;} @endphp
            @if($ot > 0)<span class="badge">{{ $ot > 99 ? '99+' : $ot }}</span>@endif
        </a>
        <a href="{{ route('admin.callcentre.index') }}" class="nav-link {{ request()->routeIs('admin.callcentre.index')||request()->routeIs('admin.callcentre.show')||request()->routeIs('admin.callcentre.create')?'active':'' }}">
            <i class="fas fa-headset"></i>Call Centre
        </a>
        <a href="{{ route('admin.notifications.index') }}" class="nav-link {{ request()->routeIs('admin.notifications.*')?'active':'' }}">
            <i class="fas fa-bell"></i>Notifications
        </a>

        <p class="nav-sec">Business</p>
        <a href="{{ route('admin.resellers.index') }}" class="nav-link {{ request()->routeIs('admin.resellers.*')?'active':'' }}">
            <i class="fas fa-store"></i>Resellers
        </a>
        <a href="{{ route('admin.operators.index') }}" class="nav-link {{ request()->routeIs('admin.operators.*')?'active':'' }}">
            <i class="fas fa-user-tie"></i>Operators
        </a>
        @php
            try { $hasPermRoute = \Illuminate\Support\Facades\Route::has('admin.permissions.index'); } catch(\Exception $e){$hasPermRoute=false;}
        @endphp
        @if($hasPermRoute)
        <a href="{{ route('admin.permissions.index') }}" class="nav-link {{ request()->routeIs('admin.permissions.*')?'active':'' }}">
            <i class="fas fa-key"></i>Permissions
        </a>
        @endif

        <p class="nav-sec">Settings</p>
        <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*')?'active':'' }}">
            <i class="fas fa-cog"></i>Settings
        </a>
        <a href="{{ route('admin.callcentre.settings') }}" class="nav-link {{ request()->is('admin/callcentre/settings')?'active':'' }}">
            <i class="fas fa-phone-alt"></i>3CX / VoIP
        </a>
        <a href="{{ route('portal.login') }}" target="_blank" class="nav-link">
            <i class="fas fa-external-link-alt"></i>Client Portal
        </a>

        <!-- Logout -->
        <div style="margin:12px 8px 4px;">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" style="width:100%;display:flex;align-items:center;gap:8px;padding:8px 14px;border-radius:8px;background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.25);color:rgba(255,255,255,.65);font-size:12.5px;font-weight:500;cursor:pointer;transition:background .15s;" onmouseover="this.style.background='rgba(239,68,68,.3)'" onmouseout="this.style.background='rgba(239,68,68,.15)'">
                    <i class="fas fa-sign-out-alt" style="width:16px;text-align:center;font-size:12px;"></i>Logout
                </button>
            </form>
        </div>

    </nav><!-- end #sidebar-nav -->
</aside><!-- end #sidebar -->


<!-- ═══════════════ MAIN CONTENT ═══════════════ -->
<div id="main-wrap">

    <!-- Top Header Bar -->
    <header style="background:#fff;border-bottom:1px solid #e2e8f0;position:sticky;top:0;z-index:30;flex-shrink:0;">
        <div style="display:flex;align-items:center;height:52px;padding:0 12px;gap:10px;">

            <!-- Hamburger (mobile/tablet) -->
            <button id="menu-btn" onclick="openSidebar()" aria-label="Open menu"
                style="display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:8px;background:none;border:none;cursor:pointer;color:#475569;flex-shrink:0;"
                onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">
                <i class="fas fa-bars" style="font-size:16px;"></i>
            </button>

            <!-- Page title -->
            <div style="flex:1;min-width:0;">
                <h1 style="font-size:15px;font-weight:900;color:#1e293b;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">@yield('page-title','Dashboard')</h1>
                <p style="font-size:11px;color:#94a3b8;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" class="hidden sm:block">@yield('page-subtitle','')</p>
            </div>

            <!-- Right actions -->
            <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                <!-- Global search (desktop) -->
                <form action="{{ route('admin.clients.index') }}" method="GET" style="display:none;" class="md:block" id="top-search-form">
                    <div style="position:relative;">
                        <i class="fas fa-search" style="position:absolute;left:9px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:11px;"></i>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search clients..." style="width:180px;border:1px solid #e2e8f0;border-radius:8px;padding:6px 10px 6px 28px;font-size:12px;outline:none;transition:width .2s;" onfocus="this.style.width='230px';this.style.borderColor='#f97316'" onblur="this.style.width='180px';this.style.borderColor='#e2e8f0'">
                    </div>
                </form>

                <!-- Notification bell -->
                @php try { $pendingT = \App\Models\SupportTicket::where('status','open')->count(); } catch(\Exception $e){$pendingT=0;} @endphp
                @if($pendingT)
                <a href="{{ route('admin.support.index') }}" style="position:relative;display:flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:8px;color:#64748b;text-decoration:none;" onmouseover="this.style.background='#fef3c7'" onmouseout="this.style.background='none'">
                    <i class="fas fa-bell" style="font-size:15px;"></i>
                    <span style="position:absolute;top:-2px;right:-2px;background:#ef4444;color:#fff;font-size:9px;font-weight:700;width:16px;height:16px;border-radius:50%;display:flex;align-items:center;justify-content:center;">{{ min($pendingT,9) }}</span>
                </a>
                @endif

                <!-- User badge -->
                <div style="display:flex;align-items:center;gap:7px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:5px 10px 5px 6px;">
                    <div style="width:24px;height:24px;border-radius:50%;background:linear-gradient(135deg,#f97316,#ea580c);display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:900;flex-shrink:0;">{{ strtoupper(substr(session('admin_user','A'),0,1)) }}</div>
                    <span style="font-size:12px;font-weight:600;color:#374151;" class="hidden sm:inline">{{ session('admin_user','Admin') }}</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    @if(session('success') || session('error') || $errors->any())
    <div style="padding:10px 14px 0;">
        @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;padding:10px 14px;border-radius:10px;font-size:13px;display:flex;align-items:center;gap:8px;">
            <i class="fas fa-check-circle"></i><span>{{ session('success') }}</span>
        </div>
        @endif
        @if(session('error'))
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:10px 14px;border-radius:10px;font-size:13px;display:flex;align-items:center;gap:8px;">
            <i class="fas fa-times-circle"></i><span>{{ session('error') }}</span>
        </div>
        @endif
        @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:10px 14px;border-radius:10px;font-size:13px;">
            <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i>{{ $errors->first() }}
        </div>
        @endif
    </div>
    @endif

    <!-- Page Content -->
    <main style="flex:1;padding:14px;overflow-x:hidden;">
        @yield('content')
    </main>

</div><!-- end #main-wrap -->


<script>
// ── Sidebar toggle ──────────────────────────────────────────────────────────
function openSidebar() {
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('overlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('overlay').classList.remove('open');
    document.body.style.overflow = '';
}

// Show top search on md+ screens
(function(){
    const form = document.getElementById('top-search-form');
    if (form && window.innerWidth >= 768) form.style.display = 'block';
    window.addEventListener('resize', function(){
        if (form) form.style.display = window.innerWidth >= 768 ? 'block' : 'none';
        // On desktop, ensure sidebar is always shown and overlay hidden
        if (window.innerWidth >= 1024) {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('overlay').classList.remove('open');
            document.body.style.overflow = '';
        }
    });
})();

// Close sidebar on ESC key
document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') closeSidebar();
});

// Scroll active nav link into view on page load
document.addEventListener('DOMContentLoaded', function(){
    const active = document.querySelector('#sidebar-nav .nav-link.active');
    if (active) {
        active.scrollIntoView({ block:'nearest', behavior:'smooth' });
    }
});
</script>

</body>
</html>
