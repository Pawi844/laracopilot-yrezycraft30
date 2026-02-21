<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OltPort extends Model {
    protected $table = 'olt_ports';
    protected $fillable = [
        'olt_device_id','port_number','port_name','onu_count','max_onu',
        'onu_status','last_seen','signal_level','fat_node_id','notes',
    ];
    protected $casts = ['last_seen'=>'datetime'];

    public function olt()     { return $this->belongsTo(OltDevice::class,'olt_device_id'); }
    public function fatNode() { return $this->belongsTo(FatNode::class,'fat_node_id'); }
    public function clients() { return $this->hasMany(IspClient::class,'onu_port','port_number'); }

    public function getIsFullAttribute(): bool { return $this->onu_count >= $this->max_onu; }
    public function getAvailableSlotsAttribute(): int { return max(0,$this->max_onu - $this->onu_count); }
    public function getStatusColorAttribute(): string {
        return match($this->onu_status) {
            'online'  => 'green',
            'offline' => 'red',
            'alarm'   => 'orange',
            default   => 'gray'
        };
    }
}