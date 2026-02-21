<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model {
    protected $fillable = ['ticket_id','user_id','client_id','message','attachment'];
    public function ticket() { return $this->belongsTo(SupportTicket::class,'ticket_id'); }
    public function user()   { return $this->belongsTo(User::class); }
    public function client() { return $this->belongsTo(IspClient::class,'client_id'); }
    public function getAuthorNameAttribute(): string {
        if ($this->user) return $this->user->name;
        if ($this->client) return $this->client->first_name.' '.$this->client->last_name;
        return 'System';
    }
}