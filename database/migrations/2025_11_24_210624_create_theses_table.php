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
        Schema::create('theses', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('supervisor_id')->constrained('users')->onDelete('cascade');
            
            // Basic information
            $table->string('title');
            $table->enum('type', ['bachelor', 'master'])->default('bachelor');
            $table->string('field_of_study')->nullable(); // e.g., Computer Science
            $table->string('specialization')->nullable(); // e.g., Software Engineering
            
            // Content
            $table->text('abstract')->nullable();
            $table->text('outline')->nullable(); // Thesis outline/scope
            $table->json('keywords')->nullable(); // Array of keywords
            
            // Status and workflow
            $table->enum('status', [
                'draft',                    // Draft - not submitted yet
                'pending_approval',         // Waiting for supervisor approval
                'approved',                 // Approved - in progress
                'under_review',            // Under review
                'returned_for_corrections', // Returned with comments
                'accepted',                // Accepted by supervisor
                'rejected',                // Rejected
                'submitted_for_defense',   // Submitted for defense
                'defended'                 // Successfully defended
            ])->default('draft');
            
            // Dates
            $table->date('submission_date')->nullable();
            $table->date('defense_date')->nullable();
            $table->string('academic_year')->nullable(); // e.g., 2024/2025
            
            // Additional metadata
            $table->text('supervisor_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes(); // Allow soft deletes for archiving
            
            // Indexes for better performance
            $table->index('status');
            $table->index('student_id');
            $table->index('supervisor_id');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theses');
    }
};
