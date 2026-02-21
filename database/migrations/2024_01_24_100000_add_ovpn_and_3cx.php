<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        // OpenVPN support on routers table
        Schema::table('routers', function (Blueprint $table) {
            if (!Schema::hasColumn('routers','use_ovpn'))         $table->boolean('use_ovpn')->default(false)->after('api_password');
            if (!Schema::hasColumn('routers','ovpn_gateway'))     $table->string('ovpn_gateway')->nullable()->after('use_ovpn'); // tunnel IP assigned to this router e.g. 10.8.0.2
            if (!Schema::hasColumn('routers','ovpn_username'))    $table->string('ovpn_username')->nullable()->after('ovpn_gateway');
            if (!Schema::hasColumn('routers','ovpn_password'))    $table->string('ovpn_password')->nullable()->after('ovpn_username');
            if (!Schema::hasColumn('routers','ovpn_status'))      $table->string('ovpn_status')->default('unknown')->after('ovpn_password'); // connected/disconnected/unknown
            if (!Schema::hasColumn('routers','ovpn_last_seen'))   $table->timestamp('ovpn_last_seen')->nullable()->after('ovpn_status');
        });

        // OpenVPN support on nas table
        Schema::table('nas', function (Blueprint $table) {
            if (!Schema::hasColumn('nas','use_ovpn'))      $table->boolean('use_ovpn')->default(false)->after('api_password');
            if (!Schema::hasColumn('nas','ovpn_gateway'))  $table->string('ovpn_gateway')->nullable()->after('use_ovpn');
        });
    }
    public function down() {}
};