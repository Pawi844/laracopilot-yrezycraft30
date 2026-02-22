<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('olt_ports')) return;
        Schema::create('olt_ports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olt_device_id')->constrained('olt_devices')->onDelete('cascade');
            $table->string('port_name');
            $table->integer('port_number');
            $table->string('status')->default('active');
            $table->integer('onu_count')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('olt_ports'); }
};