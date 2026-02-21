<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IspClient extends Model {
    use HasFactory;
    protected $table = 'clients';
    protected $fillable = ['username','password','first_name','last_name','email','phone','id_number','address','county','plan_id','nas_id','router_id','reseller_id','static_ip','mac_address','connection_type','status','expiry_date','last_seen','notify_sms','notify_email','notify_whatsapp'];
    protected $hidden = ['password'];
    protected $casts = ['expiry_date' => 'datetime', 'last_seen' => 'datetime', 'notify_sms' => 'boolean', 'notify_email' => 'boolean', 'notify_whatsapp' => 'boolean'];

    public function plan() { return $this->belongsTo(IspPlan::class, 'plan_id'); }
    public function nas() { return $this->belongsTo(Nas::class); }
    public function router() { return $this->belongsTo(Router::class); }
    public function reseller() { return $this->belongsTo(Reseller::class); }
    public function sessions() { return $this->hasMany(RadiusSession::class, 'client_id'); }
    public function tr069Device() { return $this->hasOne(Tr069Device::class, 'client_id'); }
    public function notificationLogs() { return $this->hasMany(NotificationLog::class, 'client_id'); }

    public function getFullNameAttribute() { return $this->first_name . ' ' . $this->last_name; }

    public function getActiveSessionAttribute() {
        return $this->sessions()->where('status', 'active')->latest()->first();
    }

    public function isOnline() {
        return $this->sessions()->where('status', 'active')->exists();
    }

    public function getOfflineHoursAttribute() {
        if (!$this->last_seen) return null;
        return now()->diffInHours($this->last_seen);
    }
}