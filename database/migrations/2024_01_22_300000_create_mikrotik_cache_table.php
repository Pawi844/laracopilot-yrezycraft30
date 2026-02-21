<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('mikrotik_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('router_id')->constrained('routers')->onDelete('cascade');
            $table->string('data_type'); // interfaces, ip_addresses, pppoe_active, hotspot_active, ip_pools, profiles, firewall, routes, resources
            $table->longText('data')->nullable();
            $table->timestamp('cached_at')->nullable();
            $table->timestamps();
            $table->unique(['router_id','data_type']);
        });
    }
    public function down() { Schema::dropIfExists('mikrotik_cache'); }
};