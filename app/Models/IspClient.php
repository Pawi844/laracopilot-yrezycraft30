<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IspClient extends Model
{
    use HasFactory;

    protected $table = 'isp_clients';

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'password',
        'email',
        'phone',
        'address',
        'connection_type',
        'status',
        'plan_id',
        'router_id',
        'zone_id',
        'ip_address',
        'mac_address',
        'expiry_date',
        'notes',
        'reseller_id',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function plan()
    {
        return $this->belongsTo(IspPlan::class, 'plan_id');
    }

    public function router()
    {
        return $this->belongsTo(Router::class, 'router_id');
    }

    public function zone()
    {
        return $this->belongsTo(NetworkZone::class, 'zone_id');
    }

    public function reseller()
    {
        return $this->belongsTo(Reseller::class, 'reseller_id');
    }

    public function invoices()
    {
        return $this->hasMany(ClientInvoice::class, 'client_id');
    }

    public function callLogs()
    {
        return $this->hasMany(CallLog::class, 'client_id');
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class, 'client_id');
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'active'    => 'bg-green-100 text-green-700',
            'suspended' => 'bg-yellow-100 text-yellow-700',
            'expired'   => 'bg-red-100 text-red-700',
            'pending'   => 'bg-blue-100 text-blue-700',
            default     => 'bg-gray-100 text-gray-600',
        };
    }
}