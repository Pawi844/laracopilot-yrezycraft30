<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('call_logs')) return;
        Schema::create('call_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('isp_clients')->nullOnDelete();
            $table->string('phone')->nullable();
            $table->string('type')->default('inbound');
            $table->string('status')->default('completed');
            $table->integer('duration')->default(0);
            $table->text('notes')->nullable();
            $table->string('handled_by')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('call_logs'); }
};