<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reseller extends Model {
    use HasFactory;
    protected $fillable = ['company_name','contact_name','email','phone','address','county','domain','logo','primary_color','credit_balance','commission_rate','status','allowed_features'];
    protected $casts = ['allowed_features' => 'array', 'credit_balance' => 'decimal:2'];

    public function operators() { return $this->hasMany(Operator::class); }
    public function clients() { return $this->hasMany(IspClient::class); }
    public function nas() { return $this->hasMany(Nas::class); }
    public function plans() { return $this->hasMany(IspPlan::class); }
    public function routers() { return $this->hasMany(Router::class); }
}