<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        // FAT (Fiber Access Terminal) nodes
        if (!Schema::hasTable('fat_nodes')) {
            Schema::create('fat_nodes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code', 30)->unique();
                $table->text('location')->nullable();
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->integer('max_onu')->default(32);
                $table->integer('used_onu')->default(0);
                $table->foreignId('router_id')->nullable()->constrained('routers')->nullOnDelete();
                $table->foreignId('reseller_id')->nullable()->constrained('resellers')->nullOnDelete();
                $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
                $table->enum('status', ['active','inactive','full'])->default('active');
                $table->string('olt_port')->nullable();
                $table->string('splitter_type')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // Add columns to clients table only if they don't exist
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'fat_node_id')) {
                $table->foreignId('fat_node_id')->nullable()->constrained('fat_nodes')->nullOnDelete();
            }
            if (!Schema::hasColumn('clients', 'onu_serial')) {
                $table->string('onu_serial')->nullable();
            }
            if (!Schema::hasColumn('clients', 'onu_port')) {
                $table->integer('onu_port')->nullable();
            }
            if (!Schema::hasColumn('clients', 'portal_password')) {
                $table->string('portal_password')->nullable();
            }
            if (!Schema::hasColumn('clients', 'portal_enabled')) {
                $table->boolean('portal_enabled')->default(true);
            }
            if (!Schema::hasColumn('clients', 'portal_last_login')) {
                $table->timestamp('portal_last_login')->nullable();
            }
            if (!Schema::hasColumn('clients', 'notify_sms')) {
                $table->boolean('notify_sms')->default(true);
            }
            if (!Schema::hasColumn('clients', 'notify_email')) {
                $table->boolean('notify_email')->default(true);
            }
            if (!Schema::hasColumn('clients', 'notify_whatsapp')) {
                $table->boolean('notify_whatsapp')->default(false);
            }
        });

        // Client invoices
        if (!Schema::hasTable('client_invoices')) {
            Schema::create('client_invoices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
                $table->string('invoice_no')->unique();
                $table->decimal('amount', 10, 2);
                $table->enum('status', ['pending','paid','overdue','cancelled'])->default('pending');
                $table->string('plan_name');
                $table->date('billing_period_start');
                $table->date('billing_period_end');
                $table->date('due_date');
                $table->timestamp('paid_at')->nullable();
                $table->string('payment_method')->nullable();
                $table->string('mpesa_ref')->nullable();
                $table->timestamps();
            });
        }

        // Admin permissions
        if (!Schema::hasTable('admin_permissions')) {
            Schema::create('admin_permissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('permission');
                $table->boolean('granted')->default(true);
                $table->foreignId('granted_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->unique(['user_id','permission']);
            });
        }

        // Notification schedule rules
        if (!Schema::hasTable('notification_schedules')) {
            Schema::create('notification_schedules', function (Blueprint $table) {
                $table->id();
                $table->string('event');
                $table->string('channel');
                $table->enum('timing', ['immediate','days_before','days_after','on_day'])->default('immediate');
                $table->integer('days_offset')->default(0);
                $table->time('send_at_time')->default('09:00:00');
                $table->boolean('active')->default(true);
                $table->foreignId('reseller_id')->nullable()->constrained('resellers')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down() {
        Schema::dropIfExists('notification_schedules');
        Schema::dropIfExists('admin_permissions');
        Schema::dropIfExists('client_invoices');
        Schema::table('clients', function (Blueprint $table) {
            $cols = ['fat_node_id','onu_serial','onu_port','portal_password','portal_enabled','portal_last_login','notify_sms','notify_email','notify_whatsapp'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('clients', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
        Schema::dropIfExists('fat_nodes');
    }
};