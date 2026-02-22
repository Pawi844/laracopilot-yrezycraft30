<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('offline_alerts')) return;
        Schema::create('offline_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('device_type');
            $table->unsignedBigInteger('device_id');
            $table->string('device_name');
            $table->string('ip_address')->nullable();
            $table->string('status')->default('offline');
            $table->timestamp('detected_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->boolean('notified')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('offline_alerts'); }
};