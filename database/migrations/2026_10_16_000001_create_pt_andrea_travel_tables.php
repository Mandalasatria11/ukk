<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('departments', function(Blueprint $t){$t->id();$t->string('name')->unique();$t->timestamps();});
        Schema::create('positions', function(Blueprint $t){$t->id();$t->string('name')->unique();$t->timestamps();});
        Schema::table('users', function(Blueprint $t){$t->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();$t->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();$t->string('role')->default('EMPLOYEE');$t->boolean('is_active')->default(true);});
        Schema::create('travel_policies', function(Blueprint $t){$t->id();$t->string('name');$t->foreignId('position_id')->constrained('positions');$t->string('destination_tier');$t->decimal('max_budget',14,2);$t->boolean('is_active')->default(true);$t->timestamps();});
        Schema::create('travel_requests', function(Blueprint $t){$t->id();$t->foreignId('user_id')->constrained('users');$t->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();$t->string('destination');$t->text('purpose');$t->date('start_date');$t->date('end_date');$t->decimal('estimated_cost',14,2);$t->foreignId('policy_id')->constrained('travel_policies');$t->string('status')->default('DRAFT');$t->timestamps();});
        Schema::create('approvals', function(Blueprint $t){$t->id();$t->foreignId('travel_request_id')->constrained('travel_requests')->cascadeOnDelete();$t->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete();$t->unsignedInteger('level')->default(1);$t->string('status')->default('PENDING');$t->text('note')->nullable();$t->timestamps();});
        Schema::create('travel_documents', function(Blueprint $t){$t->id();$t->foreignId('travel_request_id')->constrained('travel_requests')->cascadeOnDelete();$t->foreignId('uploaded_by')->constrained('users');$t->string('path');$t->string('original_name');$t->timestamps();});
        Schema::create('travel_bookings', function(Blueprint $t){$t->id();$t->foreignId('travel_request_id')->constrained('travel_requests')->cascadeOnDelete();$t->string('provider');$t->string('booking_type');$t->string('reference')->nullable();$t->decimal('cost',14,2);$t->string('status')->default('PENDING');$t->timestamps();});
        Schema::create('delegations', function(Blueprint $t){$t->id();$t->foreignId('delegator_id')->constrained('users');$t->foreignId('delegate_id')->constrained('users');$t->date('start_date');$t->date('end_date');$t->text('reason');$t->timestamps();});
        Schema::create('reimbursements', function(Blueprint $t){$t->id();$t->foreignId('travel_request_id')->constrained('travel_requests');$t->foreignId('user_id')->constrained('users');$t->decimal('advance_amount',14,2)->default(0);$t->decimal('approved_amount',14,2)->nullable();$t->decimal('difference_amount',14,2)->nullable();$t->string('status')->default('DRAFT');$t->string('external_journal_ref')->nullable();$t->text('finance_note')->nullable();$t->timestamps();});
        Schema::create('reimbursement_items', function(Blueprint $t){$t->id();$t->foreignId('reimbursement_id')->constrained('reimbursements')->cascadeOnDelete();$t->string('category');$t->text('description');$t->decimal('amount',14,2);$t->date('transaction_date');$t->string('receipt_path')->nullable();$t->timestamps();});
        Schema::create('notifications', function(Blueprint $t){$t->id();$t->foreignId('user_id')->constrained('users')->cascadeOnDelete();$t->string('title');$t->text('message');$t->boolean('is_read')->default(false);$t->timestamps();});
    }
    public function down(): void {
        foreach(['notifications','reimbursement_items','reimbursements','delegations','travel_bookings','travel_documents','approvals','travel_requests','travel_policies'] as $table) Schema::dropIfExists($table);
        Schema::table('users', function(Blueprint $t){$t->dropForeign(['department_id']);$t->dropForeign(['position_id']);$t->dropColumn(['department_id','position_id','role','is_active']);});
        Schema::dropIfExists('positions'); Schema::dropIfExists('departments');
    }
};
