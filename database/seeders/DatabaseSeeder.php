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
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call(HanziInertialSeeder::class);
        $this->call(HanziHSK1Seeder::class);
        $this->call(HanziHSK2Seeder::class);
        $this->call(HanziHSK3Seeder::class);
        $this->call(HanziHSK4Seeder::class);
        $this->call(HanziHSK5Seeder::class);
        $this->call(HanziHSK6Seeder::class);
        $this->call(HanziFrequentlyUsedSeeder::class);
        $this->call(HanziCommonSeeder::class);
        $this->call(HanziTraditionalSeeder::class);
        $this->call(HanziUnCommonSeeder::class);
        $this->call(HanziRadicalSeeder::class);
    }
}
