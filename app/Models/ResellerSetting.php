<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ResellerSetting extends Model {
    protected $fillable = ['reseller_id','group','key','value'];

    public function reseller() { return $this->belongsTo(Reseller::class); }

    public static function get(int $resellerId, string $group, string $key, $default = null) {
        $s = static::where('reseller_id',$resellerId)->where('group',$group)->where('key',$key)->first();
        return $s ? $s->value : $default;
    }

    public static function set(int $resellerId, string $group, string $key, $value): void {
        static::updateOrCreate(
            ['reseller_id'=>$resellerId,'group'=>$group,'key'=>$key],
            ['value'=>$value]
        );
    }
}