<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('routers')) return;
        Schema::create('routers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ip_address');
            $table->integer('api_port')->default(8728);
            $table->string('username')->default('admin');
            $table->string('password')->nullable();
            $table->string('model')->nullable();
            $table->string('location')->nullable();
            $table->boolean('use_ovpn')->default(false);
            $table->string('ovpn_gateway')->nullable();
            $table->string('ovpn_username')->nullable();
            $table->string('ovpn_password')->nullable();
            $table->boolean('active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('routers'); }
};