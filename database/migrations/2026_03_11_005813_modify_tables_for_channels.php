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
        $mainChannelId = \Illuminate\Support\Facades\DB::table('channels')->where('is_main', true)->value('id') ?? 1;

        Schema::table('broadcast_state', function (Blueprint $table) use ($mainChannelId) {
            $table->foreignId('channel_id')->default($mainChannelId)->after('id')->constrained()->cascadeOnDelete();
            $table->unique('channel_id');
        });

        Schema::table('settings', function (Blueprint $table) use ($mainChannelId) {
            $table->foreignId('channel_id')->default($mainChannelId)->after('id')->constrained()->cascadeOnDelete();
            $table->dropUnique(['key']);
            $table->unique(['channel_id', 'key']);
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->unsignedInteger('order')->default(0);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique(['channel_id', 'key']);
            $table->unique('key');
            $table->dropForeign(['channel_id']);
            $table->dropColumn('channel_id');
        });

        Schema::table('broadcast_state', function (Blueprint $table) {
            $table->dropForeign(['channel_id']);
            $table->dropColumn('channel_id');
        });
    }
};
