<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tr069Device extends Model {
    use HasFactory;
    protected $table = 'tr069_devices';
    protected $fillable = [
        'serial_number','manufacturer','model','firmware_version','hardware_version',
        'software_version','ip_address','mac_address','oui','product_class','wlan_ssid','device_id',
        'client_id','reseller_id','status','onu_status','last_inform','last_update','create_date','parameters',
        'opt_temperature','opt_voltage','opt_tx_power','opt_rx_power','opt_bias_current',
        'wan_external_ip','wan_mac_address','wan_connection_type','lan_clients'
    ];
    protected $casts = [
        'parameters'   => 'array',
        'lan_clients'  => 'array',
        'last_inform'  => 'datetime',
        'last_update'  => 'datetime',
        'create_date'  => 'datetime',
        'opt_temperature'   => 'decimal:2',
        'opt_voltage'       => 'decimal:4',
        'opt_tx_power'      => 'decimal:2',
        'opt_rx_power'      => 'decimal:2',
        'opt_bias_current'  => 'decimal:2',
    ];

    public function client()   { return $this->belongsTo(IspClient::class, 'client_id'); }
    public function reseller() { return $this->belongsTo(Reseller::class); }

    // Signal quality helper
    public function getRxSignalQualityAttribute(): string {
        $rx = (float)($this->opt_rx_power ?? 0);
        if ($rx >= -15) return 'Excellent';
        if ($rx >= -20) return 'Good';
        if ($rx >= -25) return 'Fair';
        if ($rx >= -27) return 'Weak';
        return 'Critical';
    }

    public function getRxSignalColorAttribute(): string {
        $rx = (float)($this->opt_rx_power ?? 0);
        if ($rx >= -15) return 'green';
        if ($rx >= -20) return 'green';
        if ($rx >= -25) return 'yellow';
        if ($rx >= -27) return 'orange';
        return 'red';
    }

    public function getIsOnlineAttribute(): bool {
        return $this->onu_status === 'online';
    }
}