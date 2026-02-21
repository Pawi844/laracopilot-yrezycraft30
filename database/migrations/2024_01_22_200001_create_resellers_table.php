<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('resellers', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('contact_name');
            $table->string('email')->unique();
            $table->string('phone', 20);
            $table->text('address')->nullable();
            $table->string('county', 100)->nullable();
            $table->string('domain')->nullable();
            $table->string('logo')->nullable();
            $table->string('primary_color', 20)->default('#0ea5e9');
            $table->decimal('credit_balance', 12, 2)->default(0);
            $table->decimal('commission_rate', 5, 2)->default(10);
            $table->enum('status', ['active', 'suspended', 'pending'])->default('pending');
            $table->json('allowed_features')->nullable();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('resellers'); }
};