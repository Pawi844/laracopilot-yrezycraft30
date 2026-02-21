<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('nas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('shortname', 50);
            $table->string('type', 50)->default('other');
            $table->text('description')->nullable();
            $table->string('secret');
            $table->json('ip_addresses');
            $table->string('community', 100)->default('public');
            $table->integer('ports')->default(0);
            $table->string('server', 100)->nullable();
            $table->foreignId('reseller_id')->nullable()->constrained('resellers')->onDelete('set null');
            $table->enum('status', ['active', 'inactive', 'unreachable'])->default('active');
            $table->timestamp('last_seen')->nullable();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('nas'); }
};