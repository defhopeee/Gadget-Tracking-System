<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('type')->default('phone'); // phone, tablet, laptop, smartwatch, gps_tracker, camera, other
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('imei', 32)->nullable();
            $table->string('phone_number', 32)->nullable();
            $table->string('owner_name')->nullable();
            $table->enum('status', ['active', 'inactive', 'lost', 'stolen', 'recovered'])
                  ->default('active');
            $table->string('api_key', 32)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phones');
    }
};
