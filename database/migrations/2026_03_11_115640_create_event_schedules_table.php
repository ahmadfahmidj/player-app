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
        Schema::create('event_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('location', 100)->default('');
            $table->string('subtitle', 100)->default('');
            $table->string('time_display', 100)->default('');
            $table->string('organizer', 200)->default('');
            $table->datetime('starts_at');
            $table->datetime('ends_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_schedules');
    }
};
