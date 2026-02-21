<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->enum('channel', ['sms', 'whatsapp', 'email']);
            $table->string('recipient');
            $table->text('message');
            $table->enum('status', ['sent', 'failed', 'pending'])->default('pending');
            $table->string('provider')->nullable();
            $table->string('message_id')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('notification_logs'); }
};