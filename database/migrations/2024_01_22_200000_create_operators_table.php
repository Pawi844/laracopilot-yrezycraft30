<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('operators', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone', 20)->nullable();
            $table->enum('role', ['superadmin', 'admin', 'operator', 'support'])->default('operator');
            $table->json('permissions')->nullable();
            $table->foreignId('reseller_id')->nullable()->constrained('resellers')->onDelete('set null');
            $table->boolean('active')->default(true);
            $table->timestamp('last_login')->nullable();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('operators'); }
};