<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AdminPermission extends Model {
    protected $fillable = ['user_id','permission','granted','granted_by'];
    protected $casts    = ['granted'=>'boolean'];

    public function user()      { return $this->belongsTo(User::class); }
    public function grantedBy() { return $this->belongsTo(User::class,'granted_by'); }

    public static function allPermissions(): array {
        return [
            'clients.export'    => 'Export Clients (CSV/Excel)',
            'clients.import'    => 'Import Clients (CSV)',
            'clients.delete'    => 'Delete Clients',
            'fat.manage'        => 'Manage FAT Nodes',
            'fat.assign'        => 'Assign Clients to FAT',
            'plans.manage'      => 'Manage Plans',
            'billing.manage'    => 'View & Manage Billing',
            'transactions.view' => 'View Transactions',
            'settings.view'     => 'View System Settings',
            'settings.edit'     => 'Edit System Settings',
            'operators.manage'  => 'Manage Operators',
            'resellers.manage'  => 'Manage Resellers',
            'notifications.send'=> 'Send Notifications',
            'mikrotik.manage'   => 'Access MikroTik Panel',
            'tr069.manage'      => 'Manage TR-069 Devices',
            'reports.export'    => 'Export Reports',
        ];
    }

    public static function userHas(int $userId, string $permission): bool {
        static $cache = [];
        $key = "{$userId}:{$permission}";
        if (!isset($cache[$key])) {
            $cache[$key] = static::where('user_id',$userId)->where('permission',$permission)->where('granted',true)->exists();
        }
        return $cache[$key];
    }

    public static function userPermissions(int $userId): array {
        return static::where('user_id',$userId)->where('granted',true)->pluck('permission')->toArray();
    }
}