<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RadiusSession extends Model {
    use HasFactory;
    protected $table = 'radius_sessions';
    protected $fillable = ['session_id','client_id','username','nas_ip','framed_ip','calling_station_id','called_station_id','bytes_in','bytes_out','session_time','status','terminate_cause','start_time','stop_time'];
    protected $casts = ['start_time' => 'datetime', 'stop_time' => 'datetime'];

    public function client() { return $this->belongsTo(IspClient::class, 'client_id'); }

    public function getBytesInHumanAttribute() { return $this->formatBytes($this->bytes_in); }
    public function getBytesOutHumanAttribute() { return $this->formatBytes($this->bytes_out); }
    public function getTotalBytesHumanAttribute() { return $this->formatBytes($this->bytes_in + $this->bytes_out); }

    public function getSessionTimeHumanAttribute() {
        $h = intdiv($this->session_time, 3600);
        $m = intdiv($this->session_time % 3600, 60);
        $s = $this->session_time % 60;
        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }

    private function formatBytes($bytes) {
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}