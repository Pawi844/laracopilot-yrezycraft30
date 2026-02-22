<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('mikrotik_caches')) return;
        Schema::create('mikrotik_caches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('router_id')->constrained('routers')->onDelete('cascade');
            $table->string('cache_key');
            $table->longText('cache_value')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->unique(['router_id','cache_key']);
        });
    }
    public function down(): void { Schema::dropIfExists('mikrotik_caches'); }
};