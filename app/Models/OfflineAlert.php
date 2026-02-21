<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OfflineAlert extends Model {
    use HasFactory;
    protected $table = 'offline_alerts';
    protected $fillable = ['client_id','offline_hours','notified','notified_at'];
    protected $casts = ['notified' => 'boolean', 'notified_at' => 'datetime'];

    public function client() { return $this->belongsTo(IspClient::class, 'client_id'); }
}