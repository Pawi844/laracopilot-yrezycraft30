<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('radius_sessions')) return;
        Schema::create('radius_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('username')->index();
            $table->string('session_id')->nullable();
            $table->string('nas_ip')->nullable();
            $table->string('framed_ip')->nullable();
            $table->bigInteger('bytes_in')->default(0);
            $table->bigInteger('bytes_out')->default(0);
            $table->integer('session_time')->default(0);
            $table->string('terminate_cause')->nullable();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('stop_time')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('radius_sessions'); }
};