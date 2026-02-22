<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('isp_plans')) return;
        Schema::create('isp_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('speed_down')->nullable();
            $table->string('speed_up')->nullable();
            $table->integer('validity_days')->default(30);
            $table->string('connection_type')->default('pppoe');
            $table->string('mikrotik_profile')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('isp_plans'); }
};