<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OltDevice extends Model {
    protected $table = 'olt_devices';
    protected $fillable = [
        'name','brand','model','ip_address','snmp_community','snmp_version',
        'telnet_username','telnet_password','ssh_username','ssh_password',
        'ssh_port','total_ports','router_id','location','status','notes',
    ];
    protected $casts = ['last_polled_at'=>'datetime'];

    public function router()  { return $this->belongsTo(Router::class); }
    public function ports()   { return $this->hasMany(OltPort::class,'olt_device_id'); }
    public function fatNodes(){ return $this->hasManyThrough(FatNode::class, OltPort::class, 'olt_device_id','olt_port','id','name'); }

    public function getOnlinePortsCountAttribute(): int {
        return $this->ports()->where('onu_status','online')->count();
    }
    public function getOfflinePortsCountAttribute(): int {
        return $this->ports()->where('onu_status','offline')->count();
    }
    public function getUsagePercentAttribute(): int {
        if (!$this->total_ports) return 0;
        return (int) round(($this->ports()->count() / $this->total_ports) * 100);
    }
}