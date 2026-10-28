<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->string('code', 50)->unique();
            $table->string('name', 255);
            $table->enum('level', ['province', 'regency'])->default('regency');
            $table->string('island', 50)->nullable(); // e.g. Papua, Kalimantan, Sulawesi
            $table->boolean('is_3t')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('is_3t');
            $table->index('island');
            $table->index('level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
