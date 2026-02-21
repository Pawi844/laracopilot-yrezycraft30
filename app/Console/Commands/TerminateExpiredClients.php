<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\IspClient;
use App\Models\Nas;
use App\Services\MikrotikService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class TerminateExpiredClients extends Command {
    protected $signature   = 'isp:terminate-expired {--dry-run : Show what would be terminated without doing it}';
    protected $description = 'Disconnect clients whose internet subscription has expired';

    public function handle() {
        $dry = $this->option('dry-run');
        if ($dry) $this->warn('[DRY RUN] No changes will be made.');

        // Find clients that are active but past expiry
        $expired = IspClient::with(['nas','plan'])
            ->where('status','active')
            ->whereNotNull('expiry_date')
            ->where('expiry_date','<',now()->toDateString())
            ->get();

        $this->info("Found {$expired->count()} expired active clients.");

        $notif = new NotificationService;
        $terminated = 0;
        $failed     = 0;

        foreach ($expired as $client) {
            $this->line(" → {$client->username} | Expired: {$client->expiry_date->format('d M Y')} | Plan: {$client->plan?->name}");

            if (!$dry) {
                // 1. Disconnect from MikroTik if NAS is configured
                if ($client->nas) {
                    try {
                        (new MikrotikService($client->nas))->disconnectUser($client->username);
                        $this->line("   ✓ Disconnected from MikroTik NAS: {$client->nas->shortname}");
                    } catch (\Exception $e) {
                        $this->warn("   ✗ MikroTik disconnect failed: {$e->getMessage()}");
                        Log::warning('[TerminateExpired] MikroTik error for '.$client->username.': '.$e->getMessage());
                    }
                }

                // 2. Update client status to 'expired'
                $client->update(['status'=>'expired']);

                // 3. Send expiry notification
                try {
                    $notif->notifyClient($client,'subscription_expired',[
                        'expiry' => $client->expiry_date->format('d M Y'),
                    ]);
                    $this->line('   ✓ Expiry notification sent.');
                } catch (\Exception $e) {
                    $this->warn('   ✗ Notification failed: '.$e->getMessage());
                }

                $terminated++;
            }
        }

        // Also send reminders for clients expiring in 1, 3, 7 days
        if (!$dry) {
            foreach ([7, 3, 1] as $days) {
                $expiring = IspClient::where('status','active')
                    ->whereDate('expiry_date', now()->addDays($days)->toDateString())
                    ->get();
                foreach ($expiring as $client) {
                    try {
                        $notif->notifyClient($client,'expiry_reminder',[
                            'days_left' => $days,
                            'expiry'    => $client->expiry_date->format('d M Y'),
                        ]);
                    } catch (\Exception $e) {}
                }
                if ($expiring->count()) $this->info("Sent {$days}-day reminders to {$expiring->count()} clients.");
            }
        }

        $this->info($dry ? "[DRY RUN] Would terminate {$expired->count()} clients." : "Terminated {$terminated} clients. Failed: {$failed}.");
        return 0;
    }
}