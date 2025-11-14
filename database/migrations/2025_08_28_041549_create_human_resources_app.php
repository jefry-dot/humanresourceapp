<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Departments
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Employees (butuh departments & roles)
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->string('email')->unique();
            $table->string('phone_number');
            $table->string('address');
            $table->date('hire_date');
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->string('status');
            $table->decimal('salary', 10, 2);
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Tasks
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('assigned_to')->constrained('employees')->cascadeOnDelete();
            $table->date('due_date');
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Payroll
        Schema::create('payroll', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->decimal('salary', 10, 2);
            $table->decimal('bonuses', 10, 2)->nullable();
            $table->decimal('deductions', 10, 2)->nullable();
            $table->decimal('net_salary', 10, 2);
            $table->date('pay_date');
            $table->timestamps();
            $table->softDeletes();
        });

        // 6. Presences
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->dateTime('check_in');
            $table->dateTime('check_out')->nullable();
            $table->date('date');
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });

        // 7. Leave Requests
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('leave_type');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('presences');
        Schema::dropIfExists('payroll');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('departments');
    }
};
