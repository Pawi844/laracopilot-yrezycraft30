<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Network Zones
        $zoneIds = [];
        foreach (['North District','South District','East Zone','West Zone','Central Hub'] as $z) {
            $zoneIds[] = DB::table('network_zones')->insertGetId(['name'=>$z,'city'=>'Mobilink City','active'=>1,'created_at'=>now(),'updated_at'=>now()]);
        }

        // Routers
        $routerIds = [];
        $routers = [
            ['name'=>'Core Router 1','ip_address'=>'192.168.1.1','api_port'=>8728,'username'=>'admin','password'=>'admin123','model'=>'CCR1009','location'=>'Server Room'],
            ['name'=>'Edge Router North','ip_address'=>'192.168.2.1','api_port'=>8728,'username'=>'admin','password'=>'admin123','model'=>'RB1100AHx4','location'=>'North Tower'],
            ['name'=>'Edge Router South','ip_address'=>'192.168.3.1','api_port'=>8728,'username'=>'admin','password'=>'admin123','model'=>'RB1100AHx4','location'=>'South Office'],
        ];
        foreach ($routers as $r) {
            $routerIds[] = DB::table('routers')->insertGetId(array_merge($r,['active'=>1,'use_ovpn'=>0,'created_at'=>now(),'updated_at'=>now()]));
        }

        // ISP Plans
        $planIds = [];
        $plans = [
            ['name'=>'Basic 5Mbps','price'=>15.00,'speed_down'=>'5M','speed_up'=>'2M','validity_days'=>30,'connection_type'=>'pppoe','mikrotik_profile'=>'5mbps'],
            ['name'=>'Standard 10Mbps','price'=>25.00,'speed_down'=>'10M','speed_up'=>'5M','validity_days'=>30,'connection_type'=>'pppoe','mikrotik_profile'=>'10mbps'],
            ['name'=>'Plus 20Mbps','price'=>40.00,'speed_down'=>'20M','speed_up'=>'10M','validity_days'=>30,'connection_type'=>'pppoe','mikrotik_profile'=>'20mbps'],
            ['name'=>'Premium 50Mbps','price'=>75.00,'speed_down'=>'50M','speed_up'=>'20M','validity_days'=>30,'connection_type'=>'pppoe','mikrotik_profile'=>'50mbps'],
            ['name'=>'Business 100Mbps','price'=>150.00,'speed_down'=>'100M','speed_up'=>'50M','validity_days'=>30,'connection_type'=>'pppoe','mikrotik_profile'=>'100mbps'],
            ['name'=>'Hotspot Daily','price'=>2.00,'speed_down'=>'5M','speed_up'=>'2M','validity_days'=>1,'connection_type'=>'hotspot','mikrotik_profile'=>'hotspot-daily'],
            ['name'=>'Hotspot Weekly','price'=>10.00,'speed_down'=>'10M','speed_up'=>'5M','validity_days'=>7,'connection_type'=>'hotspot','mikrotik_profile'=>'hotspot-weekly'],
        ];
        foreach ($plans as $p) {
            $planIds[] = DB::table('isp_plans')->insertGetId(array_merge($p,['active'=>1,'created_at'=>now(),'updated_at'=>now()]));
        }

        // Resellers
        $resellerIds = [];
        foreach (['Alpha Reseller','Beta Networks','City ISP','Quick Connect'] as $name) {
            $resellerIds[] = DB::table('resellers')->insertGetId(['name'=>$name,'email'=>strtolower(str_replace(' ','',$name)).'@example.com','phone'=>'+1555'.rand(1000000,9999999),'balance'=>rand(0,5000),'commission_rate'=>rand(5,15),'active'=>1,'created_at'=>now(),'updated_at'=>now()]);
        }

        // ISP Clients
        $firstNames = ['Ahmed','Sara','Mohammed','Fatima','Ali','Nour','Omar','Layla','Khalid','Mona','Hassan','Rania','Youssef','Dina','Tarek'];
        $lastNames  = ['Hassan','Ali','Mohammed','Ibrahim','Khalil','Nasser','Saad','Yousef','Mahmoud','Abdallah'];
        $statuses   = ['active','active','active','active','suspended','expired','pending'];
        $connTypes  = ['pppoe','pppoe','pppoe','hotspot','static'];
        $clientIds  = [];
        for ($i = 1; $i <= 25; $i++) {
            $fn = $firstNames[array_rand($firstNames)];
            $ln = $lastNames[array_rand($lastNames)];
            $clientIds[] = DB::table('isp_clients')->insertGetId([
                'first_name'      => $fn,
                'last_name'       => $ln,
                'username'        => strtolower($fn).'_'.strtolower($ln).'_'.$i,
                'password'        => 'pass'.rand(1000,9999),
                'email'           => strtolower($fn).rand(10,99).'@example.com',
                'phone'           => '+1555'.rand(1000000,9999999),
                'address'         => rand(1,999).' Main St, Mobilink City',
                'connection_type' => $connTypes[array_rand($connTypes)],
                'status'          => $statuses[array_rand($statuses)],
                'plan_id'         => $planIds[array_rand($planIds)],
                'router_id'       => $routerIds[array_rand($routerIds)],
                'zone_id'         => $zoneIds[array_rand($zoneIds)],
                'reseller_id'     => rand(0,1) ? $resellerIds[array_rand($resellerIds)] : null,
                'ip_address'      => '10.'.rand(0,255).'.'.rand(0,255).'.'.rand(2,254),
                'mac_address'     => implode(':',array_map(fn($x)=>strtoupper(bin2hex(random_bytes(1))),range(1,6))),
                'expiry_date'     => Carbon::now()->addDays(rand(-10,60))->toDateString(),
                'notes'           => null,
                'created_at'      => Carbon::now()->subDays(rand(1,365)),
                'updated_at'      => now(),
            ]);
        }

        // Invoices
        foreach ($clientIds as $cid) {
            for ($j = 0; $j < rand(1,4); $j++) {
                DB::table('client_invoices')->insert([
                    'client_id'      => $cid,
                    'invoice_number' => 'INV-'.date('Y').'-'.strtoupper(substr(md5(uniqid()),0,8)),
                    'amount'         => rand(15,150),
                    'paid_amount'    => rand(0,150),
                    'status'         => ['paid','unpaid','partial'][rand(0,2)],
                    'description'    => 'Monthly internet subscription',
                    'due_date'       => Carbon::now()->addDays(rand(-30,30))->toDateString(),
                    'payment_method' => ['cash','bank_transfer','card',null][rand(0,3)],
                    'created_at'     => Carbon::now()->subDays(rand(1,90)),
                    'updated_at'     => now(),
                ]);
            }
        }

        // NAS servers
        DB::table('nas')->insert([
            ['nasname'=>'192.168.1.1','shortname'=>'core-router','type'=>'other','secret'=>'testing123','description'=>'Core MikroTik','created_at'=>now(),'updated_at'=>now()],
            ['nasname'=>'192.168.2.1','shortname'=>'edge-north','type'=>'other','secret'=>'testing123','description'=>'North Edge Router','created_at'=>now(),'updated_at'=>now()],
        ]);

        // Support tickets
        $subjects = ['Cannot connect to internet','Speed is slow','IP address issue','Billing dispute','New connection request','Router reboot needed','DNS not working'];
        foreach (array_slice($clientIds,0,10) as $cid) {
            DB::table('support_tickets')->insert([
                'client_id'   => $cid,
                'subject'     => $subjects[array_rand($subjects)],
                'description' => 'Client reported issue with their connection.',
                'status'      => ['open','in_progress','resolved','closed'][rand(0,3)],
                'priority'    => ['low','normal','high','urgent'][rand(0,3)],
                'category'    => ['technical','billing','new_connection'][rand(0,2)],
                'created_at'  => Carbon::now()->subDays(rand(1,30)),
                'updated_at'  => now(),
            ]);
        }

        // Notification templates
        DB::table('notification_templates')->insert([
            ['name'=>'Expiry Reminder','type'=>'sms','event'=>'expiry','subject'=>null,'body'=>'Dear {name}, your internet subscription expires on {expiry_date}. Please renew to avoid disconnection.','active'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Payment Received','type'=>'sms','event'=>'payment','subject'=>null,'body'=>'Dear {name}, payment of {amount} received. Thank you!','active'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Welcome Message','type'=>'sms','event'=>'welcome','subject'=>null,'body'=>'Welcome {name}! Your internet connection is now active. Username: {username}','active'=>1,'created_at'=>now(),'updated_at'=>now()],
        ]);

        // OLT Devices
        $oltId = DB::table('olt_devices')->insertGetId(['name'=>'Main OLT','ip_address'=>'192.168.10.1','model'=>'HUAWEI MA5800','vendor'=>'Huawei','snmp_community'=>'public','active'=>1,'created_at'=>now(),'updated_at'=>now()]);
        for ($p = 1; $p <= 8; $p++) {
            DB::table('olt_ports')->insert(['olt_device_id'=>$oltId,'port_name'=>'GPON-0/0/'.$p,'port_number'=>$p,'status'=>'active','onu_count'=>rand(4,32),'created_at'=>now(),'updated_at'=>now()]);
        }

        // FAT nodes
        foreach (['FAT-North-01','FAT-South-01','FAT-East-01','FAT-Central-01'] as $fname) {
            DB::table('fat_nodes')->insert(['name'=>$fname,'location'=>'Pole #'.rand(100,999),'zone_id'=>$zoneIds[array_rand($zoneIds)],'capacity'=>32,'used_ports'=>rand(8,28),'status'=>'active','created_at'=>now(),'updated_at'=>now()]);
        }

        $this->command->info('✅ Database seeded successfully with all ISP Manager data!');
    }
}