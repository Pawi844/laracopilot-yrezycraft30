<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model {
    protected $fillable = ['event','channel','subject','body','active','reseller_id'];
    protected $casts = ['active' => 'boolean'];

    public function reseller() { return $this->belongsTo(Reseller::class); }

    public static function render(string $event, string $channel, array $vars, ?int $resellerId = null): ?string {
        $tpl = static::where('event',$event)->where('channel',$channel)
            ->where('active',true)
            ->where(function($q) use ($resellerId) {
                $q->where('reseller_id', $resellerId)->orWhereNull('reseller_id');
            })
            ->orderByRaw('reseller_id IS NULL ASC')
            ->first();
        if (!$tpl) return null;
        $body = $tpl->body;
        foreach ($vars as $k => $v) $body = str_replace('{'.$k.'}', $v, $body);
        return $body;
    }

    public static function events(): array {
        return [
            'welcome'           => 'New Account Created',
            'expiry_reminder'   => 'Account Expiry Reminder',
            'payment_received'  => 'Payment Received',
            'account_suspended' => 'Account Suspended',
            'account_activated' => 'Account Activated / Renewed',
            'account_expired'   => 'Account Expired',
            'low_balance'       => 'Low Balance Alert',
            'offline_alert'     => 'Device Offline Alert',
            'custom'            => 'Custom / Manual Message',
        ];
    }
}