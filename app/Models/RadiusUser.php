<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RadiusUser extends Model {
    protected $table = 'radius_users';
    protected $fillable = ['username','password','groupname','client_id','router_id','service_type','framed_ip','rate_limit','active'];
    protected $casts = ['active' => 'boolean'];

    public function client() { return $this->belongsTo(IspClient::class, 'client_id'); }
    public function router() { return $this->belongsTo(Router::class); }
}