<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IspPlan extends Model {
    use HasFactory;
    protected $table = 'isp_plans';
    protected $fillable = ['name','type','price','billing_cycle','speed_download','speed_upload','data_limit','session_timeout','idle_timeout','address_pool','profile_name','burst_limit','burst_threshold','burst_time','reseller_id','active','description'];
    protected $casts = ['price' => 'decimal:2', 'active' => 'boolean'];

    public function clients() { return $this->hasMany(IspClient::class, 'plan_id'); }
    public function reseller() { return $this->belongsTo(Reseller::class); }

    public function getSpeedLabelAttribute() {
        return ($this->speed_download ?? '?') . '↓ / ' . ($this->speed_upload ?? '?') . '↑';
    }
}