<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('needs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_profile_id')->constrained('school_profiles')->cascadeOnDelete();
            $table->foreignId('need_category_id')->constrained('need_categories')->restrictOnDelete();
            $table->string('title', 255);
            $table->text('description');
            $table->enum('assistance_type', ['material', 'non_material'])->default('material');
            $table->enum('priority', ['rendah', 'sedang', 'tinggi', 'mendesak'])->default('sedang');
            $table->unsignedInteger('quantity_required')->nullable();
            $table->unsignedInteger('quantity_fulfilled')->default(0);
            $table->string('unit', 50)->nullable(); // e.g. paket, unit, eksemplar, orang, sesi
            $table->enum('status', ['draft', 'pending_review', 'published', 'rejected', 'fulfilled', 'closed', 'archived'])->default('draft');
            $table->text('rejection_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->date('target_date')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('priority');
            $table->index('assistance_type');
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('needs');
    }
};
