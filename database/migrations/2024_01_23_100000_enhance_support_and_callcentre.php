<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        // Enhance support_tickets table
        Schema::table('support_tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('support_tickets','client_id')) {
                $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete()->after('id');
            }
            if (!Schema::hasColumn('support_tickets','technician_id')) {
                $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete()->after('client_id');
            }
            if (!Schema::hasColumn('support_tickets','assigned_by')) {
                $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete()->after('technician_id');
            }
            if (!Schema::hasColumn('support_tickets','priority')) {
                $table->enum('priority',['low','medium','high','urgent'])->default('medium')->after('status');
            }
            if (!Schema::hasColumn('support_tickets','category')) {
                $table->string('category')->nullable()->after('priority'); // connectivity, billing, equipment, other
            }
            if (!Schema::hasColumn('support_tickets','resolution')) {
                $table->text('resolution')->nullable()->after('category');
            }
            if (!Schema::hasColumn('support_tickets','resolved_at')) {
                $table->timestamp('resolved_at')->nullable()->after('resolution');
            }
            if (!Schema::hasColumn('support_tickets','fat_node_id')) {
                $table->foreignId('fat_node_id')->nullable()->constrained('fat_nodes')->nullOnDelete()->after('resolved_at');
            }
            if (!Schema::hasColumn('support_tickets','source')) {
                $table->enum('source',['call_centre','portal','admin','email','walk_in'])->default('admin')->after('fat_node_id');
            }
            if (!Schema::hasColumn('support_tickets','call_id')) {
                $table->string('call_id')->nullable()->after('source'); // linked call centre call
            }
        });

        // Ticket replies / comments
        if (!Schema::hasTable('ticket_replies')) {
            Schema::create('ticket_replies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ticket_id')->constrained('support_tickets')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // admin/tech reply
                $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete(); // client reply
                $table->text('message');
                $table->string('attachment')->nullable();
                $table->timestamps();
            });
        }

        // Call Centre — call logs
        if (!Schema::hasTable('call_logs')) {
            Schema::create('call_logs', function (Blueprint $table) {
                $table->id();
                $table->string('call_id')->unique()->nullable(); // from VoIP system
                $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
                $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('ticket_id')->nullable()->constrained('support_tickets')->nullOnDelete();
                $table->string('caller_number')->nullable();
                $table->string('direction')->default('inbound'); // inbound/outbound
                $table->enum('status',['ringing','answered','missed','voicemail','dropped'])->default('ringing');
                $table->string('disposition')->nullable(); // resolved, follow_up, escalated
                $table->integer('duration_seconds')->default(0);
                $table->text('notes')->nullable();
                $table->string('recording_url')->nullable();
                $table->timestamp('answered_at')->nullable();
                $table->timestamp('ended_at')->nullable();
                $table->timestamps();
            });
        }

        // TR-069 ACS settings (per-device provisioning)
        Schema::table('tr069_devices', function (Blueprint $table) {
            if (!Schema::hasColumn('tr069_devices','acs_url')) {
                $table->string('acs_url')->nullable()->after('serial_number');
            }
            if (!Schema::hasColumn('tr069_devices','acs_username')) {
                $table->string('acs_username')->nullable()->after('acs_url');
            }
            if (!Schema::hasColumn('tr069_devices','acs_password')) {
                $table->string('acs_password')->nullable()->after('acs_username');
            }
            if (!Schema::hasColumn('tr069_devices','connection_request_url')) {
                $table->string('connection_request_url')->nullable()->after('acs_password');
            }
            if (!Schema::hasColumn('tr069_devices','connection_request_username')) {
                $table->string('connection_request_username')->nullable()->after('connection_request_url');
            }
            if (!Schema::hasColumn('tr069_devices','connection_request_password')) {
                $table->string('connection_request_password')->nullable()->after('connection_request_username');
            }
            if (!Schema::hasColumn('tr069_devices','internet_username')) {
                $table->string('internet_username')->nullable()->after('connection_request_password'); // PPPoE/DHCP username
            }
            if (!Schema::hasColumn('tr069_devices','internet_password')) {
                $table->string('internet_password')->nullable()->after('internet_username');
            }
        });
    }

    public function down() {
        Schema::dropIfExists('call_logs');
        Schema::dropIfExists('ticket_replies');
    }
};