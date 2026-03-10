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
        Schema::table('broadcast_state', function (Blueprint $table) {
            $table->foreign('current_video_id')
                ->references('id')
                ->on('videos')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('broadcast_state', function (Blueprint $table) {
            $table->dropForeign(['current_video_id']);
        });
    }
};
