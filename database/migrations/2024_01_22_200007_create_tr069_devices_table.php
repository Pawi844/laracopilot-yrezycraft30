<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('tr069_devices', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique();
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->string('firmware_version')->nullable();
            $table->string('hardware_version')->nullable();
            $table->string('ip_address', 50)->nullable();
            $table->string('mac_address', 30)->nullable();
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->foreignId('reseller_id')->nullable()->constrained('resellers')->onDelete('set null');
            $table->enum('status', ['online', 'offline', 'unknown', 'error'])->default('unknown');
            $table->timestamp('last_inform')->nullable();
            $table->json('parameters')->nullable();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('tr069_devices'); }
};