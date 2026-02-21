<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IspClient extends Model {
    use HasFactory;
    protected $table = 'clients';

    protected $fillable = [
        'username','password','first_name','last_name','phone','email',
        'connection_type','plan_id','nas_id','router_id','fat_node_id','reseller_id',
        'static_ip','mac_address','expiry_date','status',
        'notify_sms','notify_email','notify_whatsapp',
        'portal_password','portal_enabled','portal_last_login',
        'onu_serial','onu_port','address','notes',
    ];

    protected $casts = [
        'expiry_date'       => 'date',
        'portal_last_login' => 'datetime',
        'notify_sms'        => 'boolean',
        'notify_email'      => 'boolean',
        'notify_whatsapp'   => 'boolean',
        'portal_enabled'    => 'boolean',
    ];

    // ── Relationships ────────────────────────────────────────────────────
    public function plan()     { return $this->belongsTo(Plan::class); }
    public function nas()      { return $this->belongsTo(Nas::class); }
    public function router()   { return $this->belongsTo(Router::class); }
    public function reseller() { return $this->belongsTo(Reseller::class); }
    public function invoices() { return $this->hasMany(ClientInvoice::class,'client_id'); }
    public function tickets()  { return $this->hasMany(SupportTicket::class,'client_id'); }
    public function devices()  { return $this->hasMany(Tr069Device::class,'client_id'); }
    public function sessions() { return $this->hasMany(RadiusSession::class,'username','username'); }

    /**
     * FAT node relationship — aliased as both fat() and fatNode()
     * so both ->fat and ->fatNode work throughout the app
     */
    public function fat()     { return $this->belongsTo(FatNode::class,'fat_node_id'); }
    public function fatNode() { return $this->belongsTo(FatNode::class,'fat_node_id'); }

    // ── Helpers ──────────────────────────────────────────────────────────
    public function getFullNameAttribute(): string {
        return trim(($this->first_name ?? '').' '.($this->last_name ?? ''));
    }

    public function isExpired(): bool {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getDaysLeftAttribute(): int {
        if (!$this->expiry_date) return 9999;
        return (int) now()->diffInDays($this->expiry_date, false);
    }
}