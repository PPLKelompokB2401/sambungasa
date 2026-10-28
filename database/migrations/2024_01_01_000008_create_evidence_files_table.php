<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evidence_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pledge_id')->constrained('pledges')->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->string('file_path', 255);
            $table->string('original_name', 255);
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size');
            $table->enum('evidence_type', [
                'delivery_receipt',
                'photo_documentation',
                'activity_report',
                'handover_bast',
                'other'
            ])->default('photo_documentation');
            $table->text('caption')->nullable();
            $table->timestamps();

            $table->index('pledge_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidence_files');
    }
};
