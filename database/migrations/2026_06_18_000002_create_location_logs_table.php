<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('location_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phone_id')->constrained()->cascadeOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('accuracy', 8, 2)->nullable();   // metres
            $table->unsignedTinyInteger('battery')->nullable(); // 0-100 %
            $table->string('label', 120)->nullable();
            $table->enum('source', ['device', 'manual', 'browser'])->default('manual');
            $table->timestamps();

            $table->index(['phone_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location_logs');
    }
};
