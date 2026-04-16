<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@hospital.local'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
            ]
        );

        \App\Models\BroadcastState::firstOrCreate(
            ['id' => 1],
            [
                'current_video_id' => null,
                'current_position' => 0,
                'is_playing' => false,
                'loop_mode' => 'none',
                'started_at' => null,
            ]
        );

        \App\Models\Setting::upsert([
            ['key' => 'running_text', 'value' => 'Selamat datang di Rumah Sakit', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'logo_path', 'value' => null, 'created_at' => now(), 'updated_at' => now()],
        ], ['key']);
    }
}
