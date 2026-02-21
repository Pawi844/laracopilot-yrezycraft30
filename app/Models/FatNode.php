<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FatNode extends Model {
    protected $table = 'fat_nodes';
    protected $fillable = [
        'name','code','location','latitude','longitude',
        'max_onu','used_onu','router_id','reseller_id','technician_id',
        'status','olt_port','splitter_type','notes',
    ];

    public function router()     { return $this->belongsTo(Router::class); }
    public function reseller()   { return $this->belongsTo(Reseller::class); }
    public function technician() { return $this->belongsTo(User::class,'technician_id'); }
    public function clients()    { return $this->hasMany(IspClient::class,'fat_node_id'); }
    public function devices()    { return $this->hasMany(Tr069Device::class,'fat_node_id'); }

    public function getAvailableSlotsAttribute(): int {
        return max(0, $this->max_onu - $this->used_onu);
    }
    public function getUsagePercentAttribute(): int {
        if (!$this->max_onu) return 0;
        return (int) round(($this->used_onu / $this->max_onu) * 100);
    }
    public function recalculateUsed(): void {
        $this->update(['used_onu' => $this->clients()->count()]);
    }
    public function getIsFullAttribute(): bool {
        return $this->used_onu >= $this->max_onu;
    }
}