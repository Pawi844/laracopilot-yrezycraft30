<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RadiusTrafficLog extends Model {
    public $timestamps = false;
    protected $table = 'radius_traffic_logs';
    protected $fillable = ['client_id','bytes_in','bytes_out','bytes_in_delta','bytes_out_delta','session_id','nas_ip','polled_at'];
    protected $casts = ['polled_at' => 'datetime'];

    public function client() { return $this->belongsTo(IspClient::class,'client_id'); }

    public static function forGraph(int $clientId, int $points = 30): array {
        return static::where('client_id',$clientId)
            ->orderBy('polled_at','desc')
            ->limit($points)
            ->get()
            ->reverse()
            ->values()
            ->map(fn($r) => [
                'time'       => $r->polled_at->format('H:i:s'),
                'bytes_in'   => (int)$r->bytes_in_delta,
                'bytes_out'  => (int)$r->bytes_out_delta,
                'total_in'   => (int)$r->bytes_in,
                'total_out'  => (int)$r->bytes_out,
            ])->toArray();
    }
}