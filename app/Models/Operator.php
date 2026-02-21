<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Operator extends Model {
    use HasFactory;
    protected $fillable = ['name','username','email','password','phone','role','permissions','reseller_id','active','last_login'];
    protected $hidden = ['password'];
    protected $casts = ['permissions' => 'array', 'active' => 'boolean', 'last_login' => 'datetime'];

    public function reseller() { return $this->belongsTo(Reseller::class); }

    public function hasPermission($permission) {
        if ($this->role === 'superadmin' || $this->role === 'admin') return true;
        return in_array($permission, $this->permissions ?? []);
    }
}