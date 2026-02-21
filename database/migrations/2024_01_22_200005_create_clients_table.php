<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('id_number', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('county', 100)->nullable();
            $table->foreignId('plan_id')->nullable()->constrained('isp_plans')->onDelete('set null');
            $table->foreignId('nas_id')->nullable()->constrained('nas')->onDelete('set null');
            $table->foreignId('router_id')->nullable()->constrained('routers')->onDelete('set null');
            $table->foreignId('reseller_id')->nullable()->constrained('resellers')->onDelete('set null');
            $table->string('static_ip')->nullable();
            $table->string('mac_address', 20)->nullable();
            $table->enum('connection_type', ['pppoe', 'hotspot', 'static'])->default('pppoe');
            $table->enum('status', ['active', 'inactive', 'suspended', 'expired'])->default('active');
            $table->timestamp('expiry_date')->nullable();
            $table->timestamp('last_seen')->nullable();
            $table->boolean('notify_sms')->default(true);
            $table->boolean('notify_email')->default(true);
            $table->boolean('notify_whatsapp')->default(false);
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('clients'); }
};