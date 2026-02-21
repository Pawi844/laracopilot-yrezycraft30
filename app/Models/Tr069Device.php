<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tr069Device extends Model {
    use HasFactory;
    protected $table = 'tr069_devices';
    protected $fillable = ['serial_number','manufacturer','model','firmware_version','hardware_version','ip_address','mac_address','client_id','reseller_id','status','last_inform','parameters'];
    protected $casts = ['parameters' => 'array', 'last_inform' => 'datetime'];

    public function client() { return $this->belongsTo(IspClient::class, 'client_id'); }
    public function reseller() { return $this->belongsTo(Reseller::class); }
}