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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('thesis_id')->constrained()->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            
            // File information
            $table->string('filename'); // Stored filename (UUID)
            $table->string('original_name'); // Original filename from user
            $table->string('path'); // Storage path
            $table->unsignedBigInteger('size'); // File size in bytes
            $table->string('mime_type'); // MIME type (application/pdf, etc.)
            $table->string('extension', 10); // File extension
            
            // Metadata
            $table->text('description')->nullable();
            $table->integer('version')->default(1); // For future versioning
            
            $table->timestamps();
            
            // Indexes
            $table->index('thesis_id');
            $table->index('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
