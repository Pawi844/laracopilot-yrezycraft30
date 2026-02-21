<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50);   // mpesa, sms, whatsapp, mail, general, billing
            $table->string('key', 100);
            $table->text('value')->nullable();
            $table->string('type', 20)->default('text'); // text,password,select,toggle,textarea
            $table->string('label', 150)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_secret')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['group','key']);
        });

        Schema::create('reseller_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseller_id')->constrained('resellers')->onDelete('cascade');
            $table->string('group', 50);
            $table->string('key', 100);
            $table->text('value')->nullable();
            $table->timestamps();
            $table->unique(['reseller_id','group','key']);
        });

        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('event', 100);  // expiry_reminder, payment_received, account_suspended, welcome, custom
            $table->enum('channel', ['sms','email','whatsapp']);
            $table->string('subject')->nullable(); // email only
            $table->text('body');  // supports {name},{username},{expiry},{amount},{plan},{ip}
            $table->boolean('active')->default(true);
            $table->foreignId('reseller_id')->nullable()->constrained('resellers')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['event','channel','reseller_id']);
        });

        Schema::create('radius_traffic_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->bigInteger('bytes_in')->default(0);
            $table->bigInteger('bytes_out')->default(0);
            $table->bigInteger('bytes_in_delta')->default(0);  // change since last poll
            $table->bigInteger('bytes_out_delta')->default(0);
            $table->string('session_id')->nullable();
            $table->string('nas_ip')->nullable();
            $table->timestamp('polled_at');
            $table->index(['client_id','polled_at']);
        });
    }
    public function down() {
        Schema::dropIfExists('radius_traffic_logs');
        Schema::dropIfExists('notification_templates');
        Schema::dropIfExists('reseller_settings');
        Schema::dropIfExists('system_settings');
    }
};