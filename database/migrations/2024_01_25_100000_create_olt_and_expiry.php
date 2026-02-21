<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        // OLT Devices
        if (!Schema::hasTable('olt_devices')) {
            Schema::create('olt_devices', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('brand')->default('tenda'); // tenda,visol,hioso,huawei,zte
                $table->string('model')->nullable();
                $table->string('ip_address');
                $table->string('snmp_community')->default('public');
                $table->string('snmp_version')->default('2c');
                $table->string('telnet_user')->nullable();
                $table->string('telnet_password')->nullable();
                $table->string('ssh_user')->nullable();
                $table->string('ssh_password')->nullable();
                $table->integer('total_ports')->default(16);
                $table->integer('port_capacity')->default(64); // ONUs per port
                $table->enum('status',['online','offline','degraded','unknown'])->default('unknown');
                $table->string('location')->nullable();
                $table->foreignId('reseller_id')->nullable()->constrained('resellers')->nullOnDelete();
                $table->foreignId('router_id')->nullable()->constrained('routers')->nullOnDelete();
                $table->timestamp('last_polled')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
        // Add olt_id to fat_nodes
        Schema::table('fat_nodes', function (Blueprint $table) {
            if (!Schema::hasColumn('fat_nodes','olt_id')) {
                $table->foreignId('olt_id')->nullable()->constrained('olt_devices')->nullOnDelete()->after('splitter_type');
            }
            if (!Schema::hasColumn('fat_nodes','olt_port_number')) {
                $table->integer('olt_port_number')->nullable()->after('olt_id');
            }
        });
        // wlan_password on tr069_devices
        Schema::table('tr069_devices', function (Blueprint $table) {
            if (!Schema::hasColumn('tr069_devices','wlan_password')) {
                $table->string('wlan_password')->nullable()->after('wlan_ssid');
            }
            if (!Schema::hasColumn('tr069_devices','signal_level')) {
                $table->string('signal_level')->nullable();
            }
            if (!Schema::hasColumn('tr069_devices','last_seen')) {
                $table->timestamp('last_seen')->nullable();
            }
        });
    }
    public function down() {
        Schema::dropIfExists('olt_devices');
    }
};