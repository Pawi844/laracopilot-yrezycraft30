<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MikrotikCache extends Model {
    protected $table = 'mikrotik_cache';
    protected $fillable = ['router_id','data_type','data','cached_at'];
    protected $casts = ['cached_at' => 'datetime'];

    public function router() { return $this->belongsTo(Router::class); }

    public function getDataArrayAttribute() {
        return json_decode($this->data, true) ?? [];
    }

    public function isStale(int $minutes = 2): bool {
        if (!$this->cached_at) return true;
        return $this->cached_at->diffInMinutes(now()) >= $minutes;
    }
}