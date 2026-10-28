<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('school_name', 255);
            $table->string('npsn', 20)->nullable();
            $table->text('address');
            $table->foreignId('province_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->foreignId('regency_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->string('district', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('phone', 25)->nullable();
            $table->string('principal_name', 255)->nullable();
            $table->unsignedInteger('student_count')->nullable();
            $table->unsignedInteger('teacher_count')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('verification_status', ['pending', 'verified', 'rejected', 'needs_revision'])->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            $table->string('photo_path', 255)->nullable();
            $table->timestamps();

            $table->index('verification_status');
            $table->index('regency_id');
            $table->index('province_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_profiles');
    }
};
