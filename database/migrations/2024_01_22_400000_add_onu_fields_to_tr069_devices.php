<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('tr069_devices', function (Blueprint $table) {
            // General
            $table->string('oui', 20)->nullable()->after('mac_address');
            $table->string('product_class', 100)->nullable()->after('oui');
            $table->string('software_version', 100)->nullable()->after('hardware_version');
            $table->string('wlan_ssid', 100)->nullable()->after('software_version');
            $table->string('device_id', 200)->nullable()->after('wlan_ssid');
            $table->timestamp('last_update')->nullable()->after('last_inform');
            $table->timestamp('create_date')->nullable()->after('last_update');
            $table->enum('onu_status', ['online', 'offline', 'unknown'])->default('unknown')->after('status');

            // Optical
            $table->decimal('opt_temperature', 8, 2)->nullable()->after('onu_status');
            $table->decimal('opt_voltage', 8, 4)->nullable()->after('opt_temperature');
            $table->decimal('opt_tx_power', 8, 2)->nullable()->after('opt_voltage');
            $table->decimal('opt_rx_power', 8, 2)->nullable()->after('opt_tx_power');
            $table->decimal('opt_bias_current', 8, 2)->nullable()->after('opt_rx_power');

            // WAN PPP
            $table->string('wan_external_ip', 50)->nullable()->after('opt_bias_current');
            $table->string('wan_mac_address', 30)->nullable()->after('wan_external_ip');
            $table->string('wan_connection_type', 50)->nullable()->after('wan_mac_address');

            // Connected clients (JSON array)
            $table->json('lan_clients')->nullable()->after('wan_connection_type');
        });
    }
    public function down() {
        Schema::table('tr069_devices', function (Blueprint $table) {
            $table->dropColumn([
                'oui','product_class','software_version','wlan_ssid','device_id',
                'last_update','create_date','onu_status',
                'opt_temperature','opt_voltage','opt_tx_power','opt_rx_power','opt_bias_current',
                'wan_external_ip','wan_mac_address','wan_connection_type','lan_clients'
            ]);
        });
    }
};