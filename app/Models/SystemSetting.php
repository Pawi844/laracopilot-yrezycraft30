<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model {
    protected $fillable = ['group','key','value','type','label','description','is_secret','sort_order'];
    protected $casts = ['is_secret' => 'boolean'];

    public static function get(string $group, string $key, $default = null) {
        return Cache::remember("setting:{$group}:{$key}", 300, function () use ($group, $key, $default) {
            $s = static::where('group',$group)->where('key',$key)->first();
            return $s ? $s->value : $default;
        });
    }

    public static function set(string $group, string $key, $value): void {
        static::updateOrCreate(['group'=>$group,'key'=>$key], ['value'=>$value]);
        Cache::forget("setting:{$group}:{$key}");
    }

    public static function group(string $group): array {
        return static::where('group',$group)->orderBy('sort_order')->get()
            ->mapWithKeys(fn($s) => [$s->key => $s->value])->toArray();
    }

    public static function allGroups(): array {
        return static::orderBy('group')->orderBy('sort_order')->get()
            ->groupBy('group')->toArray();
    }
}