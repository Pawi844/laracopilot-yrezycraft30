<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('radius_users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('groupname')->nullable();
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->foreignId('router_id')->nullable()->constrained('routers')->onDelete('set null');
            $table->enum('service_type', ['pppoe','hotspot','static'])->default('pppoe');
            $table->string('framed_ip')->nullable();
            $table->string('rate_limit')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('radius_groups', function (Blueprint $table) {
            $table->id();
            $table->string('groupname')->unique();
            $table->string('attribute');
            $table->string('op', 2)->default(':=');
            $table->string('value');
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('radius_groups');
        Schema::dropIfExists('radius_users');
    }
};