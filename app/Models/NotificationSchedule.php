<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class NotificationSchedule extends Model {
    protected $fillable = ['event','channel','timing','days_offset','send_at_time','active','reseller_id'];
    protected $casts    = ['active'=>'boolean'];

    public function reseller() { return $this->belongsTo(Reseller::class); }

    public static function eventOptions(): array {
        return [
            'expiry_reminder'   => 'Account Expiry Reminder',
            'account_expired'   => 'Account Expired',
            'account_suspended' => 'Account Suspended',
            'payment_received'  => 'Payment Received',
            'welcome'           => 'Welcome / Account Created',
            'low_balance'       => 'Low Balance Alert',
        ];
    }
}