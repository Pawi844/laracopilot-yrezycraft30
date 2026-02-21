<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Router extends Model {
    use HasFactory;
    protected $fillable = ['name','ip_address','api_port','username','password','model','firmware','nas_id','reseller_id','status','last_sync','interfaces'];
    protected $hidden = ['password'];
    protected $casts = ['interfaces' => 'array', 'last_sync' => 'datetime'];

    public function nas() { return $this->belongsTo(Nas::class); }
    public function reseller() { return $this->belongsTo(Reseller::class); }
    public function clients() { return $this->hasMany(IspClient::class); }
}