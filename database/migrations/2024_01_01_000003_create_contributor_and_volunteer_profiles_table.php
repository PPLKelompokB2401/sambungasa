<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contributor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->enum('contributor_type', ['donor', 'volunteer', 'organization'])->default('donor');
            $table->string('organization_name', 255)->nullable();
            $table->string('pic_name', 255)->nullable();
            $table->string('phone', 25)->nullable();
            $table->text('address')->nullable();
            $table->string('identity_number', 50)->nullable(); // Private: NIK/NPWP/Legal doc num
            $table->string('identity_document_path', 255)->nullable(); // Private storage path
            $table->string('website', 255)->nullable();
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            $table->timestamps();

            $table->index('contributor_type');
            $table->index('verification_status');
        });

        Schema::create('volunteer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contributor_profile_id')->unique()->constrained('contributor_profiles')->cascadeOnDelete();
            $table->string('education', 255)->nullable();
            $table->string('expertise', 255)->nullable();
            $table->unsignedInteger('experience_years')->default(0);
            $table->enum('availability', ['flexible', 'weekends', 'full_time', 'remote_only'])->default('flexible');
            $table->text('bio')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('volunteer_profiles');
        Schema::dropIfExists('contributor_profiles');
    }
};
