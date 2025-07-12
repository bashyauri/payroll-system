<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('staff_id')->unique();
            $table->string('name');
            $table->unsignedBigInteger('department_id');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Link to users table (optional)
            $table->string('level')->nullable();
            $table->string('step')->nullable();
            $table->decimal('basic_salary', 10, 2);
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->timestamps();

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};