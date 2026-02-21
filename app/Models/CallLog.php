<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CallLog extends Model {
    protected $fillable = [
        'call_id','client_id','agent_id','ticket_id','caller_number','direction',
        'status','disposition','duration_seconds','notes','recording_url','answered_at','ended_at'
    ];
    protected $casts = ['answered_at'=>'datetime','ended_at'=>'datetime'];

    public function client()  { return $this->belongsTo(IspClient::class,'client_id'); }
    public function agent()   { return $this->belongsTo(User::class,'agent_id'); }
    public function ticket()  { return $this->belongsTo(SupportTicket::class,'ticket_id'); }

    public function getDurationFormattedAttribute(): string {
        $s = $this->duration_seconds;
        return gmdate($s >= 3600 ? 'H:i:s' : 'i:s', $s);
    }
    public function getStatusColorAttribute(): string {
        return match($this->status) {
            'answered' => 'green','missed' => 'red','ringing' => 'yellow','voicemail' => 'purple', default => 'gray'
        };
    }
}