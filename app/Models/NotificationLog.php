<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationLog extends Model {
    use HasFactory;
    protected $table = 'notification_logs';
    protected $fillable = ['client_id','channel','recipient','message','status','provider','message_id','error'];

    public function client() { return $this->belongsTo(IspClient::class, 'client_id'); }
}