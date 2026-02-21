<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Tr069Device extends Model {
    protected $table = 'tr069_devices';
    protected $fillable = [
        'serial_number','mac_address','model','client_id','fat_node_id','router_id',
        'acs_url','acs_username','acs_password',
        'connection_request_url','connection_request_username','connection_request_password',
        'internet_username','internet_password','wlan_ssid','wlan_password',
        'onu_status','onu_port','signal_level','last_seen',
    ];
    protected $casts = ['last_seen'=>'datetime'];

    // ── Relationships ──────────────────────────────────────────────────────
    public function client()   { return $this->belongsTo(IspClient::class, 'client_id'); }
    public function fatNode()  { return $this->belongsTo(FatNode::class,   'fat_node_id'); }
    public function router()   { return $this->belongsTo(Router::class,    'router_id'); }

    // alias so both ->fat and ->fatNode work in views
    public function fat()      { return $this->fatNode(); }
}