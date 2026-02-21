<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('network_zones', function (Blueprint $table) {
            $table->id();
            $table->string('county', 100);
            $table->string('area', 255);
            $table->enum('coverage_type', ['4g', '5g', 'fiber', 'wimax', 'satellite']);
            $table->enum('status', ['active', 'planned', 'maintenance', 'limited'])->default('active');
            $table->integer('signal_strength')->default(3);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('network_zones');
    }
};