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
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@hospital.local',
            'password' => bcrypt('password'),
        ]);

        \App\Models\BroadcastState::create([
            'current_video_id' => null,
            'current_position' => 0,
            'is_playing' => false,
            'loop_mode' => 'none',
            'started_at' => null,
            'updated_at' => now(),
        ]);

        \App\Models\Setting::insert([
            ['key' => 'running_text', 'value' => 'Selamat datang di Rumah Sakit', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'logo_path', 'value' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
