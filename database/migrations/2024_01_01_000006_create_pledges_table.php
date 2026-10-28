<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pledges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('need_id')->constrained('needs')->restrictOnDelete();
            $table->foreignId('contributor_profile_id')->constrained('contributor_profiles')->cascadeOnDelete();
            $table->string('tracking_code', 50)->unique();
            $table->unsignedInteger('quantity')->nullable();
            $table->text('notes')->nullable();
            $table->date('estimated_delivery_at')->nullable();
            $table->enum('status', [
                'submitted',
                'verified',
                'in_delivery',
                'received',
                'completed',
                'rejected',
                'cancelled'
            ])->default('submitted');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('tracking_code');
            $table->index('status');
            $table->index('need_id');
            $table->index('contributor_profile_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pledges');
    }
};
