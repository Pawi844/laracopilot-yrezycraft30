<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('isp_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['pppoe', 'hotspot', 'static'])->default('pppoe');
            $table->decimal('price', 10, 2);
            $table->enum('billing_cycle', ['hourly', 'daily', 'weekly', 'monthly', 'quarterly', 'yearly']);
            $table->string('speed_download')->nullable();
            $table->string('speed_upload')->nullable();
            $table->string('data_limit')->nullable();
            $table->integer('session_timeout')->nullable()->comment('seconds');
            $table->integer('idle_timeout')->nullable()->comment('seconds');
            $table->string('address_pool')->nullable();
            $table->string('profile_name')->nullable();
            $table->string('burst_limit')->nullable();
            $table->string('burst_threshold')->nullable();
            $table->string('burst_time')->nullable();
            $table->foreignId('reseller_id')->nullable()->constrained('resellers')->onDelete('set null');
            $table->boolean('active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('isp_plans'); }
};