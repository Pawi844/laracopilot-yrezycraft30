<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NasController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\OperatorController;
use App\Http\Controllers\Admin\ResellerController;
use App\Http\Controllers\Admin\HotspotController;
use App\Http\Controllers\Admin\RouterController;
use App\Http\Controllers\Admin\Tr069Controller;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\MikrotikController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ResellerSettingsController;
use App\Http\Controllers\PublicController;

// Public
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/services', [PublicController::class, 'services'])->name('services');
Route::get('/plans', [PublicController::class, 'plans'])->name('plans');
Route::get('/about', [PublicController::class, 'about'])->name('about');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
Route::post('/contact', [PublicController::class, 'submitContact'])->name('contact.submit');
Route::get('/coverage', [PublicController::class, 'coverage'])->name('coverage');

// Admin Auth
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Dashboard
Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

// NAS
Route::get('/admin/nas', [NasController::class, 'index'])->name('admin.nas.index');
Route::get('/admin/nas/create', [NasController::class, 'create'])->name('admin.nas.create');
Route::post('/admin/nas', [NasController::class, 'store'])->name('admin.nas.store');
Route::get('/admin/nas/{id}', [NasController::class, 'show'])->name('admin.nas.show');
Route::get('/admin/nas/{id}/edit', [NasController::class, 'edit'])->name('admin.nas.edit');
Route::put('/admin/nas/{id}', [NasController::class, 'update'])->name('admin.nas.update');
Route::delete('/admin/nas/{id}', [NasController::class, 'destroy'])->name('admin.nas.destroy');
Route::post('/admin/nas/{id}/test', [NasController::class, 'testConnection'])->name('admin.nas.test');

// Plans
Route::get('/admin/plans', [PlanController::class, 'index'])->name('admin.plans.index');
Route::get('/admin/plans/create', [PlanController::class, 'create'])->name('admin.plans.create');
Route::post('/admin/plans', [PlanController::class, 'store'])->name('admin.plans.store');
Route::get('/admin/plans/{id}/edit', [PlanController::class, 'edit'])->name('admin.plans.edit');
Route::put('/admin/plans/{id}', [PlanController::class, 'update'])->name('admin.plans.update');
Route::delete('/admin/plans/{id}', [PlanController::class, 'destroy'])->name('admin.plans.destroy');

// Hotspot
Route::get('/admin/hotspot', [HotspotController::class, 'index'])->name('admin.hotspot.index');
Route::get('/admin/hotspot/create', [HotspotController::class, 'create'])->name('admin.hotspot.create');
Route::post('/admin/hotspot', [HotspotController::class, 'store'])->name('admin.hotspot.store');
Route::get('/admin/hotspot/{id}/edit', [HotspotController::class, 'edit'])->name('admin.hotspot.edit');
Route::put('/admin/hotspot/{id}', [HotspotController::class, 'update'])->name('admin.hotspot.update');
Route::delete('/admin/hotspot/{id}', [HotspotController::class, 'destroy'])->name('admin.hotspot.destroy');

// Clients
Route::get('/admin/clients', [ClientController::class, 'index'])->name('admin.clients.index');
Route::get('/admin/clients/create', [ClientController::class, 'create'])->name('admin.clients.create');
Route::post('/admin/clients', [ClientController::class, 'store'])->name('admin.clients.store');
Route::get('/admin/clients/{id}', [ClientController::class, 'show'])->name('admin.clients.show');
Route::get('/admin/clients/{id}/edit', [ClientController::class, 'edit'])->name('admin.clients.edit');
Route::put('/admin/clients/{id}', [ClientController::class, 'update'])->name('admin.clients.update');
Route::delete('/admin/clients/{id}', [ClientController::class, 'destroy'])->name('admin.clients.destroy');
Route::post('/admin/clients/{id}/disconnect', [ClientController::class, 'disconnect'])->name('admin.clients.disconnect');
Route::post('/admin/clients/{id}/reconnect', [ClientController::class, 'reconnect'])->name('admin.clients.reconnect');
Route::get('/admin/clients/{id}/traffic', [ClientController::class, 'trafficData'])->name('admin.clients.traffic');

// Sessions
Route::get('/admin/sessions', [SessionController::class, 'index'])->name('admin.sessions.index');
Route::get('/admin/sessions/live', [SessionController::class, 'live'])->name('admin.sessions.live');
Route::delete('/admin/sessions/{id}', [SessionController::class, 'destroy'])->name('admin.sessions.destroy');

// ─── System Settings ──────────────────────────────────────────────────
Route::get('/admin/settings', [SettingsController::class, 'index'])->name('admin.settings.index');
Route::get('/admin/settings/{group}', [SettingsController::class, 'group'])->name('admin.settings.group');
Route::put('/admin/settings/{group}', [SettingsController::class, 'updateGroup'])->name('admin.settings.update');
Route::post('/admin/settings/mail/test', [SettingsController::class, 'testMail'])->name('admin.settings.test_mail');
// Notification Templates
Route::get('/admin/settings/templates', [SettingsController::class, 'templates'])->name('admin.settings.templates');
Route::get('/admin/settings/templates/create', [SettingsController::class, 'createTemplate'])->name('admin.settings.templates.create');
Route::post('/admin/settings/templates', [SettingsController::class, 'storeTemplate'])->name('admin.settings.templates.store');
Route::get('/admin/settings/templates/{id}/edit', [SettingsController::class, 'editTemplate'])->name('admin.settings.templates.edit');
Route::put('/admin/settings/templates/{id}', [SettingsController::class, 'updateTemplate'])->name('admin.settings.templates.update');
Route::delete('/admin/settings/templates/{id}', [SettingsController::class, 'destroyTemplate'])->name('admin.settings.templates.destroy');

// MikroTik
Route::get('/admin/mikrotik', [MikrotikController::class, 'selectRouter'])->name('admin.mikrotik.select');
Route::get('/admin/mikrotik/setup-guide', [MikrotikController::class, 'setupGuide'])->name('admin.mikrotik.setup');
Route::get('/admin/mikrotik/{routerId}/dashboard', [MikrotikController::class, 'dashboard'])->name('admin.mikrotik.dashboard');
Route::get('/admin/mikrotik/{routerId}/interfaces', [MikrotikController::class, 'interfaces'])->name('admin.mikrotik.interfaces');
Route::get('/admin/mikrotik/{routerId}/pppoe', [MikrotikController::class, 'pppoe'])->name('admin.mikrotik.pppoe');
Route::post('/admin/mikrotik/{routerId}/pppoe/disconnect', [MikrotikController::class, 'disconnectPppoe'])->name('admin.mikrotik.pppoe.disconnect');
Route::post('/admin/mikrotik/{routerId}/pppoe/add', [MikrotikController::class, 'addPppoeSecret'])->name('admin.mikrotik.pppoe.add');
Route::post('/admin/mikrotik/{routerId}/pppoe/delete', [MikrotikController::class, 'deletePppoeSecret'])->name('admin.mikrotik.pppoe.delete');
Route::get('/admin/mikrotik/{routerId}/hotspot', [MikrotikController::class, 'hotspot'])->name('admin.mikrotik.hotspot');
Route::post('/admin/mikrotik/{routerId}/hotspot/disconnect', [MikrotikController::class, 'disconnectHotspot'])->name('admin.mikrotik.hotspot.disconnect');
Route::get('/admin/mikrotik/{routerId}/ip-pools', [MikrotikController::class, 'ipPools'])->name('admin.mikrotik.ip_pools');
Route::get('/admin/mikrotik/{routerId}/queues', [MikrotikController::class, 'queues'])->name('admin.mikrotik.queues');
Route::get('/admin/mikrotik/{routerId}/firewall', [MikrotikController::class, 'firewall'])->name('admin.mikrotik.firewall');
Route::get('/admin/mikrotik/{routerId}/dhcp', [MikrotikController::class, 'dhcp'])->name('admin.mikrotik.dhcp');
Route::get('/admin/mikrotik/{routerId}/routes', [MikrotikController::class, 'routes'])->name('admin.mikrotik.routes');
Route::get('/admin/mikrotik/{routerId}/wireless', [MikrotikController::class, 'wireless'])->name('admin.mikrotik.wireless');
Route::get('/admin/mikrotik/{routerId}/radius', [MikrotikController::class, 'radius'])->name('admin.mikrotik.radius');
Route::post('/admin/mikrotik/{routerId}/radius/push', [MikrotikController::class, 'pushRadiusConfig'])->name('admin.mikrotik.radius.push');
Route::post('/admin/mikrotik/{routerId}/sync-users', [MikrotikController::class, 'syncUsers'])->name('admin.mikrotik.sync');

// Routers
Route::get('/admin/routers', [RouterController::class, 'index'])->name('admin.routers.index');
Route::get('/admin/routers/create', [RouterController::class, 'create'])->name('admin.routers.create');
Route::post('/admin/routers', [RouterController::class, 'store'])->name('admin.routers.store');
Route::get('/admin/routers/{id}/edit', [RouterController::class, 'edit'])->name('admin.routers.edit');
Route::put('/admin/routers/{id}', [RouterController::class, 'update'])->name('admin.routers.update');
Route::delete('/admin/routers/{id}', [RouterController::class, 'destroy'])->name('admin.routers.destroy');
Route::post('/admin/routers/{id}/sync', [RouterController::class, 'sync'])->name('admin.routers.sync');

// TR-069
Route::get('/admin/tr069', [Tr069Controller::class, 'index'])->name('admin.tr069.index');
Route::get('/admin/tr069/create', [Tr069Controller::class, 'create'])->name('admin.tr069.create');
Route::post('/admin/tr069', [Tr069Controller::class, 'store'])->name('admin.tr069.store');
Route::get('/admin/tr069/{id}', [Tr069Controller::class, 'show'])->name('admin.tr069.show');
Route::get('/admin/tr069/{id}/edit', [Tr069Controller::class, 'edit'])->name('admin.tr069.edit');
Route::put('/admin/tr069/{id}', [Tr069Controller::class, 'update'])->name('admin.tr069.update');
Route::delete('/admin/tr069/{id}', [Tr069Controller::class, 'destroy'])->name('admin.tr069.destroy');
Route::post('/admin/tr069/{id}/reboot', [Tr069Controller::class, 'reboot'])->name('admin.tr069.reboot');
Route::post('/admin/tr069/{id}/refresh', [Tr069Controller::class, 'refreshFromAcs'])->name('admin.tr069.refresh');

// Operators
Route::get('/admin/operators', [OperatorController::class, 'index'])->name('admin.operators.index');
Route::get('/admin/operators/create', [OperatorController::class, 'create'])->name('admin.operators.create');
Route::post('/admin/operators', [OperatorController::class, 'store'])->name('admin.operators.store');
Route::get('/admin/operators/{id}/edit', [OperatorController::class, 'edit'])->name('admin.operators.edit');
Route::put('/admin/operators/{id}', [OperatorController::class, 'update'])->name('admin.operators.update');
Route::delete('/admin/operators/{id}', [OperatorController::class, 'destroy'])->name('admin.operators.destroy');

// Resellers
Route::get('/admin/resellers', [ResellerController::class, 'index'])->name('admin.resellers.index');
Route::get('/admin/resellers/create', [ResellerController::class, 'create'])->name('admin.resellers.create');
Route::post('/admin/resellers', [ResellerController::class, 'store'])->name('admin.resellers.store');
Route::get('/admin/resellers/{id}', [ResellerController::class, 'show'])->name('admin.resellers.show');
Route::get('/admin/resellers/{id}/edit', [ResellerController::class, 'edit'])->name('admin.resellers.edit');
Route::put('/admin/resellers/{id}', [ResellerController::class, 'update'])->name('admin.resellers.update');
Route::delete('/admin/resellers/{id}', [ResellerController::class, 'destroy'])->name('admin.resellers.destroy');
// Reseller Settings & Templates
Route::get('/admin/resellers/{id}/settings', [ResellerSettingsController::class, 'index'])->name('admin.resellers.settings');
Route::put('/admin/resellers/{id}/settings', [ResellerSettingsController::class, 'update'])->name('admin.resellers.settings.update');
Route::get('/admin/resellers/{id}/templates', [ResellerSettingsController::class, 'templates'])->name('admin.resellers.templates');
Route::post('/admin/resellers/{id}/templates', [ResellerSettingsController::class, 'storeTemplate'])->name('admin.resellers.templates.store');
Route::delete('/admin/resellers/{id}/templates/{tplId}', [ResellerSettingsController::class, 'destroyTemplate'])->name('admin.resellers.templates.destroy');

// Notifications
Route::get('/admin/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
Route::post('/admin/notifications/sms', [NotificationController::class, 'sendSms'])->name('admin.notifications.sms');
Route::post('/admin/notifications/whatsapp', [NotificationController::class, 'sendWhatsapp'])->name('admin.notifications.whatsapp');
Route::post('/admin/notifications/email', [NotificationController::class, 'sendEmail'])->name('admin.notifications.email');
Route::post('/admin/notifications/broadcast', [NotificationController::class, 'broadcast'])->name('admin.notifications.broadcast');

// Services
Route::get('/admin/services', [ServiceController::class, 'index'])->name('admin.services.index');
Route::get('/admin/services/create', [ServiceController::class, 'create'])->name('admin.services.create');
Route::post('/admin/services', [ServiceController::class, 'store'])->name('admin.services.store');
Route::get('/admin/services/{id}/edit', [ServiceController::class, 'edit'])->name('admin.services.edit');
Route::put('/admin/services/{id}', [ServiceController::class, 'update'])->name('admin.services.update');
Route::delete('/admin/services/{id}', [ServiceController::class, 'destroy'])->name('admin.services.destroy');

// Transactions
Route::get('/admin/transactions', [TransactionController::class, 'index'])->name('admin.transactions.index');
Route::get('/admin/transactions/create', [TransactionController::class, 'create'])->name('admin.transactions.create');
Route::post('/admin/transactions', [TransactionController::class, 'store'])->name('admin.transactions.store');
Route::get('/admin/transactions/{id}', [TransactionController::class, 'show'])->name('admin.transactions.show');

// Support
Route::get('/admin/support', [SupportController::class, 'index'])->name('admin.support.index');
Route::get('/admin/support/{id}', [SupportController::class, 'show'])->name('admin.support.show');
Route::put('/admin/support/{id}', [SupportController::class, 'update'])->name('admin.support.update');
Route::delete('/admin/support/{id}', [SupportController::class, 'destroy'])->name('admin.support.destroy');