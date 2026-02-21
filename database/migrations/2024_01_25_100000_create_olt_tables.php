<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        if (!Schema::hasTable('olt_devices')) {
            Schema::create('olt_devices', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('brand')->default('Huawei'); // Huawei, ZTE, Calix, Nokia
                $table->string('model')->nullable();
                $table->string('ip_address');
                $table->string('snmp_community')->default('public');
                $table->string('snmp_version')->default('2c');
                $table->string('telnet_username')->nullable();
                $table->string('telnet_password')->nullable();
                $table->string('ssh_username')->nullable();
                $table->string('ssh_password')->nullable();
                $table->integer('ssh_port')->default(22);
                $table->integer('total_ports')->default(16);
                $table->foreignId('router_id')->nullable()->constrained('routers')->nullOnDelete();
                $table->string('location')->nullable();
                $table->enum('status',['online','offline','unknown'])->default('unknown');
                $table->string('notes')->nullable();
                $table->timestamp('last_polled_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('olt_ports')) {
            Schema::create('olt_ports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('olt_device_id')->constrained('olt_devices')->onDelete('cascade');
                $table->integer('port_number');
                $table->string('port_name')->nullable(); // GPON 0/1, etc
                $table->integer('onu_count')->default(0);
                $table->integer('max_onu')->default(128);
                $table->enum('onu_status',['online','offline','alarm','unknown'])->default('unknown');
                $table->string('signal_level')->nullable(); // dBm
                $table->foreignId('fat_node_id')->nullable()->constrained('fat_nodes')->nullOnDelete();
                $table->string('notes')->nullable();
                $table->timestamp('last_seen')->nullable();
                $table->timestamps();
                $table->unique(['olt_device_id','port_number']);
            });
        }
    }
    public function down() {
        Schema::dropIfExists('olt_ports');
        Schema::dropIfExists('olt_devices');
    }
};