<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HotelUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('hotel_user')->delete();

        DB::table('hotel_user')->insert([
            0 => [
                "id" => 1,
                "user_id" => 3,
                "hotel_id" => 2,
                "created_at" => now(),
                "updated_at" => now()
            ],
            1 => [
                "id" => 2,
                "user_id" => 4,
                "hotel_id" => 2,
                "created_at" => now(),
                "updated_at" => now()
            ],
            2 => [
                "id" => 3,
                "user_id" => 5,
                "hotel_id" => 6,
                "created_at" => now(),
                "updated_at" => now()
            ],
            3 => [
                "id" => 4,
                "user_id" => 5,
                "hotel_id" => 7,
                "created_at" => now(),
                "updated_at" => now()
            ],
            4 => [
                "id" => 6,
                "user_id" => 5,
                "hotel_id" => 8,
                "created_at" => now(),
                "updated_at" => now()
            ],
        ]);
    }
}
