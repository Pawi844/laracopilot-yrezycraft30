<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Nas extends Model {
    use HasFactory;
    protected $table = 'nas';
    protected $fillable = ['name','shortname','type','description','secret','ip_addresses','community','ports','server','reseller_id','status','last_seen'];
    protected $casts = ['ip_addresses' => 'array', 'last_seen' => 'datetime'];

    public function reseller() { return $this->belongsTo(Reseller::class); }
    public function routers() { return $this->hasMany(Router::class); }
    public function clients() { return $this->hasMany(IspClient::class); }
    public function sessions() { return $this->hasMany(RadiusSession::class, 'nas_ip'); }

    public function getPrimaryIpAttribute() {
        return $this->ip_addresses[0] ?? 'N/A';
    }
}