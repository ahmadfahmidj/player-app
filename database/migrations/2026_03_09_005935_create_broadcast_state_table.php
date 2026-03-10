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
        Schema::create('broadcast_state', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('current_video_id')->nullable();
            $table->decimal('current_position', 10, 3)->default(0);
            $table->boolean('is_playing')->default(false);
            $table->enum('loop_mode', ['none', 'single', 'playlist'])->default('none');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('broadcast_state');
    }
};
