<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model {
    protected $table = 'support_tickets';
    protected $fillable = [
        'client_id','technician_id','assigned_by','subject','description','status','priority',
        'category','resolution','resolved_at','fat_node_id','source','call_id',
        'name','email','phone', // walk-in / non-client tickets
    ];
    protected $casts = ['resolved_at'=>'datetime'];

    public function client()      { return $this->belongsTo(IspClient::class,'client_id'); }
    public function technician()  { return $this->belongsTo(User::class,'technician_id'); }
    public function assignedBy()  { return $this->belongsTo(User::class,'assigned_by'); }
    public function fatNode()     { return $this->belongsTo(FatNode::class,'fat_node_id'); }
    public function replies()     { return $this->hasMany(TicketReply::class,'ticket_id'); }
    public function callLog()     { return $this->hasOne(CallLog::class,'ticket_id'); }

    public function getPriorityColorAttribute(): string {
        return match($this->priority) {
            'urgent' => 'red','high' => 'orange','medium' => 'yellow','low' => 'green', default => 'gray'
        };
    }
    public function getStatusColorAttribute(): string {
        return match($this->status) {
            'open' => 'blue','in_progress' => 'yellow','resolved' => 'green','closed' => 'gray', default => 'gray'
        };
    }
    public static function categories(): array {
        return ['connectivity'=>'Connectivity Issue','billing'=>'Billing / Payment','equipment'=>'Equipment / ONU Fault','speed'=>'Slow Speed','wifi'=>'WiFi Issue','other'=>'Other'];
    }
}