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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            
            // User who receives the notification
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Notification content
            $table->string('type'); // 'thesis_approved', 'thesis_rejected', 'new_message', etc.
            $table->string('title');
            $table->text('message')->nullable();
            $table->string('icon')->nullable(); // emoji or icon class
            $table->string('color')->default('gray'); // gray, green, red, blue, yellow
            
            // Action link
            $table->string('action_url')->nullable();
            
            // Related models (optional, for filtering)
            $table->unsignedBigInteger('related_thesis_id')->nullable();
            $table->unsignedBigInteger('related_message_id')->nullable();
            
            // Read status
            $table->timestamp('read_at')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'read_at']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
