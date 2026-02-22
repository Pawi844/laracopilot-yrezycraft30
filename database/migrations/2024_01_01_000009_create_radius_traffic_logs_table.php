<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('radius_traffic_logs')) return;
        Schema::create('radius_traffic_logs', function (Blueprint $table) {
            $table->id();
            $table->string('username')->index();
            $table->bigInteger('bytes_in')->default(0);
            $table->bigInteger('bytes_out')->default(0);
            $table->date('log_date')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('radius_traffic_logs'); }
};