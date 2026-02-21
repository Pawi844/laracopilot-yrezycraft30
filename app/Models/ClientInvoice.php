<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ClientInvoice extends Model {
    protected $fillable = [
        'client_id','invoice_no','amount','status','plan_name',
        'billing_period_start','billing_period_end','due_date','paid_at','payment_method','mpesa_ref'
    ];
    protected $casts = [
        'billing_period_start'=>'date','billing_period_end'=>'date',
        'due_date'=>'date','paid_at'=>'datetime','amount'=>'decimal:2'
    ];
    public function client() { return $this->belongsTo(IspClient::class,'client_id'); }
    public function getStatusColorAttribute(): string {
        return match($this->status) {
            'paid'=>'green','pending'=>'yellow','overdue'=>'red','cancelled'=>'gray', default=>'gray'
        };
    }
    public static function nextInvoiceNo(): string {
        $prefix = \App\Models\SystemSetting::get('billing','invoice_prefix','INV');
        $last   = static::latest()->first();
        $num    = $last ? (intval(substr($last->invoice_no,-5))+1) : 1;
        return $prefix.'-'.date('Y').'-'.str_pad($num,5,'0',STR_PAD_LEFT);
    }
}