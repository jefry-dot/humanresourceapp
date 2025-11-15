<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            // Add missing columns
            $table->text('reason')->after('end_date');
            $table->foreignId('approved_by')->nullable()->after('status')->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable()->after('approved_by');

            // Modify leave_type to enum
            DB::statement("ALTER TABLE leave_requests MODIFY leave_type ENUM('annual', 'sick', 'personal', 'maternity', 'paternity', 'unpaid') NOT NULL");

            // Modify status to enum with default
            DB::statement("ALTER TABLE leave_requests MODIFY status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['reason', 'approved_by', 'approved_at']);

            // Revert to varchar
            DB::statement("ALTER TABLE leave_requests MODIFY leave_type VARCHAR(255) NOT NULL");
            DB::statement("ALTER TABLE leave_requests MODIFY status VARCHAR(255) NOT NULL");
        });
    }
};
