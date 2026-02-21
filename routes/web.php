<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NasController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ClientImportExportController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\OperatorController;
use App\Http\Controllers\Admin\ResellerController;
use App\Http\Controllers\Admin\HotspotController;
use App\Http\Controllers\Admin\RouterController;
use App\Http\Controllers\Admin\Tr069Controller;
use App\Http\Controllers\Admin\OltController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\NotificationScheduleController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\MikrotikController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\FatNodeController;
use App\Http\Controllers\Admin\MpesaController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\CallCentreController;
use App\Http\Controllers\Portal\PortalAuthController;
use App\Http\Controllers\Portal\PortalController;
use App\Http\Controllers\PublicController;

// Public
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/services', [PublicController::class, 'services'])->name('services');
Route::get('/plans', [PublicController::class, 'plans'])->name('plans');
Route::get('/about', [PublicController::class, 'about'])->name('about');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
Route::post('/contact', [PublicController::class, 'submitContact'])->name('contact.submit');
Route::get('/coverage', [PublicController::class, 'coverage'])->name('coverage');

// M-Pesa
Route::post('/api/mpesa/callback', [MpesaController::class, 'callback'])->name('mpesa.callback');
Route::post('/api/mpesa/validation', [MpesaController::class, 'validation']);
Route::post('/api/mpesa/confirmation', [MpesaController::class, 'confirmation']);
Route::post('/admin/mpesa/stk-push', [MpesaController::class, 'stkPush'])->name('admin.mpesa.stk');

// 3CX Webhook
Route::post('/api/callcentre/webhook', [CallCentreController::class, 'webhook'])->name('callcentre.webhook');

// Portal
Route::get('/portal', [PortalAuthController::class, 'showLogin'])->name('portal.login');
Route::post('/portal/login', [PortalAuthController::class, 'login']);
Route::post('/portal/logout', [PortalAuthController::class, 'logout'])->name('portal.logout');
Route::get('/portal/dashboard', [PortalController::class, 'dashboard'])->name('portal.dashboard');
Route::get('/portal/bills', [PortalController::class, 'bills'])->name('portal.bills');
Route::get('/portal/utilization', [PortalController::class, 'utilization'])->name('portal.utilization');
Route::get('/portal/topup', [PortalController::class, 'topup'])->name('portal.topup');
Route::get('/portal/change-plan', [PortalController::class, 'changePlan'])->name('portal.change_plan');
Route::post('/portal/change-plan', [PortalController::class, 'submitChangePlan'])->name('portal.submit_plan');
Route::get('/portal/devices', [PortalController::class, 'devices'])->name('portal.devices');
Route::post('/portal/devices/wifi', [PortalController::class, 'changeWifiPassword'])->name('portal.change_wifi');
Route::get('/portal/profile', [PortalController::class, 'profile'])->name('portal.profile');
Route::post('/portal/profile', [PortalController::class, 'updateProfile'])->name('portal.update_profile');
Route::get('/portal/traffic', [PortalController::class, 'liveTraffic'])->name('portal.live_traffic');

// Admin Auth
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Dashboard
Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

// Clients
Route::get('/admin/clients', [ClientController::class, 'index'])->name('admin.clients.index');
Route::get('/admin/clients/create', [ClientController::class, 'create'])->name('admin.clients.create');
Route::get('/admin/clients/export', [ClientImportExportController::class, 'export'])->name('admin.clients.export');
Route::get('/admin/clients/import', [ClientImportExportController::class, 'showImport'])->name('admin.clients.import');
Route::post('/admin/clients/import', [ClientImportExportController::class, 'import']);
Route::get('/admin/clients/import/template', [ClientImportExportController::class, 'downloadTemplate'])->name('admin.clients.import.template');
Route::post('/admin/clients', [ClientController::class, 'store'])->name('admin.clients.store');
Route::get('/admin/clients/{id}', [ClientController::class, 'show'])->name('admin.clients.show');
Route::get('/admin/clients/{id}/edit', [ClientController::class, 'edit'])->name('admin.clients.edit');
Route::put('/admin/clients/{id}', [ClientController::class, 'update'])->name('admin.clients.update');
Route::delete('/admin/clients/{id}', [ClientController::class, 'destroy'])->name('admin.clients.destroy');
Route::post('/admin/clients/{id}/disconnect', [ClientController::class, 'disconnect'])->name('admin.clients.disconnect');
Route::post('/admin/clients/{id}/reconnect', [ClientController::class, 'reconnect'])->name('admin.clients.reconnect');
Route::get('/admin/clients/{id}/traffic', [ClientController::class, 'trafficData'])->name('admin.clients.traffic');

// FAT Nodes
Route::get('/admin/fat', [FatNodeController::class, 'index'])->name('admin.fat.index');
Route::get('/admin/fat/create', [FatNodeController::class, 'create'])->name('admin.fat.create');
Route::post('/admin/fat', [FatNodeController::class, 'store'])->name('admin.fat.store');
Route::get('/admin/fat/{id}', [FatNodeController::class, 'show'])->name('admin.fat.show');
Route::get('/admin/fat/{id}/edit', [FatNodeController::class, 'edit'])->name('admin.fat.edit');
Route::put('/admin/fat/{id}', [FatNodeController::class, 'update'])->name('admin.fat.update');
Route::delete('/admin/fat/{id}', [FatNodeController::class, 'destroy'])->name('admin.fat.destroy');

// OLT Devices
Route::get('/admin/olt', [OltController::class, 'index'])->name('admin.olt.index');
Route::get('/admin/olt/create', [OltController::class, 'create'])->name('admin.olt.create');
Route::post('/admin/olt', [OltController::class, 'store'])->name('admin.olt.store');
Route::get('/admin/olt/{id}', [OltController::class, 'show'])->name('admin.olt.show');
Route::get('/admin/olt/{id}/edit', [OltController::class, 'edit'])->name('admin.olt.edit');
Route::put('/admin/olt/{id}', [OltController::class, 'update'])->name('admin.olt.update');
Route::delete('/admin/olt/{id}', [OltController::class, 'destroy'])->name('admin.olt.destroy');
Route::post('/admin/olt/{id}/poll', [OltController::class, 'pollOlt'])->name('admin.olt.poll');
Route::put('/admin/olt/{oltId}/ports/{portId}', [OltController::class, 'updatePort'])->name('admin.olt.update_port');

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

// Sessions
Route::get('/admin/sessions', [SessionController::class, 'index'])->name('admin.sessions.index');
Route::get('/admin/sessions/live', [SessionController::class, 'live'])->name('admin.sessions.live');
Route::delete('/admin/sessions/{id}', [SessionController::class, 'destroy'])->name('admin.sessions.destroy');

// Permissions
Route::get('/admin/permissions', [PermissionController::class, 'index'])->name('admin.permissions.index');
Route::get('/admin/permissions/{userId}', [PermissionController::class, 'show'])->name('admin.permissions.show');
Route::put('/admin/permissions/{userId}', [PermissionController::class, 'update'])->name('admin.permissions.update');

// Call Centre
Route::get('/admin/callcentre', [CallCentreController::class, 'index'])->name('admin.callcentre.index');
Route::get('/admin/callcentre/create', [CallCentreController::class, 'create'])->name('admin.callcentre.create');
Route::post('/admin/callcentre', [CallCentreController::class, 'store'])->name('admin.callcentre.store');
Route::get('/admin/callcentre/settings', [CallCentreController::class, 'settings'])->name('admin.callcentre.settings');
Route::get('/admin/callcentre/lookup', [CallCentreController::class, 'lookupPhone'])->name('admin.callcentre.lookup');
Route::get('/admin/callcentre/{id}', [CallCentreController::class, 'show'])->name('admin.callcentre.show');
Route::delete('/admin/callcentre/{id}', [CallCentreController::class, 'destroy'])->name('admin.callcentre.destroy');

// Settings
Route::get('/admin/settings', [SettingsController::class, 'index'])->name('admin.settings.index');
Route::get('/admin/settings/templates', [SettingsController::class, 'templates'])->name('admin.settings.templates');
Route::get('/admin/settings/templates/create', [SettingsController::class, 'createTemplate'])->name('admin.settings.templates.create');
Route::post('/admin/settings/templates', [SettingsController::class, 'storeTemplate'])->name('admin.settings.templates.store');
Route::get('/admin/settings/templates/{id}/edit', [SettingsController::class, 'editTemplate'])->name('admin.settings.templates.edit');
Route::put('/admin/settings/templates/{id}', [SettingsController::class, 'updateTemplate'])->name('admin.settings.templates.update');
Route::delete('/admin/settings/templates/{id}', [SettingsController::class, 'destroyTemplate'])->name('admin.settings.templates.destroy');
Route::get('/admin/settings/{group}', [SettingsController::class, 'group'])->name('admin.settings.group');
Route::put('/admin/settings/{group}', [SettingsController::class, 'updateGroup'])->name('admin.settings.update');
Route::post('/admin/settings/mail/test', [SettingsController::class, 'testMail'])->name('admin.settings.test_mail');

// Notification Schedule
Route::get('/admin/notifications/schedule', [NotificationScheduleController::class, 'index'])->name('admin.notifications.schedule');
Route::post('/admin/notifications/schedule', [NotificationScheduleController::class, 'store'])->name('admin.notifications.schedule.store');
Route::put('/admin/notifications/schedule/{id}', [NotificationScheduleController::class, 'update'])->name('admin.notifications.schedule.update');
Route::delete('/admin/notifications/schedule/{id}', [NotificationScheduleController::class, 'destroy'])->name('admin.notifications.schedule.destroy');

// Notifications
Route::get('/admin/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
Route::post('/admin/notifications/sms', [NotificationController::class, 'sendSms'])->name('admin.notifications.sms');
Route::post('/admin/notifications/whatsapp', [NotificationController::class, 'sendWhatsapp'])->name('admin.notifications.whatsapp');
Route::post('/admin/notifications/email', [NotificationController::class, 'sendEmail'])->name('admin.notifications.email');
Route::post('/admin/notifications/broadcast', [NotificationController::class, 'broadcast'])->name('admin.notifications.broadcast');

// MikroTik
Route::get('/admin/mikrotik', [MikrotikController::class, 'selectRouter'])->name('admin.mikrotik.select');
Route::get('/admin/mikrotik/setup-guide', [MikrotikController::class, 'setupGuide'])->name('admin.mikrotik.setup');
Route::get('/admin/mikrotik/{routerId}/dashboard', [MikrotikController::class, 'dashboard'])->name('admin.mikrotik.dashboard');
Route::get('/admin/mikrotik/{routerId}/pppoe', [MikrotikController::class, 'pppoe'])->name('admin.mikrotik.pppoe');
Route::post('/admin/mikrotik/{routerId}/pppoe/disconnect', [MikrotikController::class, 'disconnectPppoe'])->name('admin.mikrotik.pppoe.disconnect');
Route::post('/admin/mikrotik/{routerId}/pppoe/add', [MikrotikController::class, 'addPppoeSecret'])->name('admin.mikrotik.pppoe.add');
Route::post('/admin/mikrotik/{routerId}/pppoe/delete', [MikrotikController::class, 'deletePppoeSecret'])->name('admin.mikrotik.pppoe.delete');
Route::get('/admin/mikrotik/{routerId}/hotspot', [MikrotikController::class, 'hotspot'])->name('admin.mikrotik.hotspot');
Route::post('/admin/mikrotik/{routerId}/hotspot/disconnect', [MikrotikController::class, 'disconnectHotspot'])->name('admin.mikrotik.hotspot.disconnect');
Route::get('/admin/mikrotik/{routerId}/queues', [MikrotikController::class, 'queues'])->name('admin.mikrotik.queues');
Route::get('/admin/mikrotik/{routerId}/firewall', [MikrotikController::class, 'firewall'])->name('admin.mikrotik.firewall');
Route::get('/admin/mikrotik/{routerId}/dhcp', [MikrotikController::class, 'dhcp'])->name('admin.mikrotik.dhcp');
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
Route::get('/admin/routers/{id}/ovpn-config', [RouterController::class, 'downloadOvpnConfig'])->name('admin.routers.ovpn_config');

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
Route::post('/admin/tr069/{id}/push-internet', [Tr069Controller::class, 'pushInternetSettings'])->name('admin.tr069.push_internet');

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
Route::get('/admin/support/create', [SupportController::class, 'create'])->name('admin.support.create');
Route::post('/admin/support', [SupportController::class, 'store'])->name('admin.support.store');
Route::get('/admin/support/{id}', [SupportController::class, 'show'])->name('admin.support.show');
Route::put('/admin/support/{id}', [SupportController::class, 'update'])->name('admin.support.update');
Route::post('/admin/support/{id}/reply', [SupportController::class, 'reply'])->name('admin.support.reply');
Route::post('/admin/support/{id}/assign', [SupportController::class, 'assign'])->name('admin.support.assign');
Route::delete('/admin/support/{id}', [SupportController::class, 'destroy'])->name('admin.support.destroy');