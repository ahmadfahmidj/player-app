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
        Schema::create('channel_video', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('video_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        $mainChannel = \Illuminate\Support\Facades\DB::table('channels')->where('is_main', true)->first();
        if ($mainChannel) {
            $videos = \Illuminate\Support\Facades\DB::table('videos')->orderBy('order')->get();
            foreach ($videos as $video) {
                \Illuminate\Support\Facades\DB::table('channel_video')->insert([
                    'channel_id' => $mainChannel->id,
                    'video_id' => $video->id,
                    'order' => $video->order,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_video');
    }
};
