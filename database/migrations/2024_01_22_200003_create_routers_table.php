<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('routers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ip_address');
            $table->integer('api_port')->default(8728);
            $table->string('username');
            $table->string('password');
            $table->string('model')->nullable();
            $table->string('firmware')->nullable();
            $table->foreignId('nas_id')->nullable()->constrained('nas')->onDelete('set null');
            $table->foreignId('reseller_id')->nullable()->constrained('resellers')->onDelete('set null');
            $table->enum('status', ['online', 'offline', 'unknown'])->default('unknown');
            $table->timestamp('last_sync')->nullable();
            $table->json('interfaces')->nullable();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('routers'); }
};