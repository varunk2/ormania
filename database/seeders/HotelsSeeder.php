<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HotelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('hotels')->delete();

        DB::table('hotels')->insert([
            0 => [
                "id" => 1,
                "name" => 'Daiwik Hotels',
                "slug" => 'daiwik_hotels',
                "city" => "Rameswaram",
                "country" => "India",
                "image" => 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/2a/92/f1/7d/caption.jpg?w=1000&h=-1&s=1',
                "price_per_night" => '₹8,500',
                "user_id" => 1,
                "created_at" => now(),
                "updated_at" => now()
            ],
            1 => [
                "id" => 2,
                "name" => 'Hotel Star Palace',
                "slug" => 'hotel_star_palace',
                "city" => "Rameswaram",
                "country" => "India",
                "image" => 'https://www.starpalacehotels.com/wp-content/uploads/2022/12/single-gallery-image-1.jpg',
                "price_per_night" => '₹6,200',
                "user_id" => 1,
                "created_at" => now(),
                "updated_at" => now()
            ],
            2 => [
                "id" => 3,
                "name" => 'Hotel Aalayam Rameshwaram',
                "slug" => 'hotel_aalayam_rameshwaram',
                "city" => "Rameswaram",
                "country" => "India",
                "image" => 'https://www.rameshwaramhotels.com/data/Pics/OriginalPhoto/14691/1469151/1469151749/hotel-aalayam-rameshwaram-rameshwaram-pic-2.JPEG',
                "price_per_night" => '₹4,500',
                "user_id" => 1,
                "created_at" => now(),
                "updated_at" => now()
            ],
            3 => [
                "id" => 4,
                "name" => 'jüSTa Sarang, Rameswaram',
                "slug" => 'justa_sarang_rameswaram',
                "city" => "Rameswaram",
                "country" => "India",
                "image" => 'https://www.justahotels.com/wp-content/uploads/2022/12/Luxe-Cliffend-1240x562-1.png',
                "price_per_night" => '₹4,500',
                "user_id" => 1,
                "created_at" => now(),
                "updated_at" => now()
            ],
            4 => [
                "id" => 5,
                "name" => 'Avanti Kalagram',
                "slug" => 'avanti_kalagram',
                "city" => "Pune",
                "country" => "India",
                "image" => "https://static.wixstatic.com/media/78f03b_e1177b87b44d4050bb17fea763b79aac~mv2.jpg/v1/fit/w_750,h_600,q_90,enc_avif,quality_auto/78f03b_e1177b87b44d4050bb17fea763b79aac~mv2.jpg",
                "price_per_night" => '₹4,500',
                "user_id" => 1,
                "created_at" => now(),
                "updated_at" => now()
            ],
            5 => [
                "id" => 6,
                "name" => 'Forganic Farm Produce & Agro Tourism',
                "slug" => 'forganic_farm_produce_agro_tourism',
                "city" => "Maharashtra",
                "country" => "India",
                "image" => "https://lh3.googleusercontent.com/p/AF1QipNoDfl5pgaFmfEEFR-gV700v_OpmPg4MrBQvMa_=s680-w680-h510-rw",
                "price_per_night" => '₹4,500',
                "user_id" => 1,
                "created_at" => now(),
                "updated_at" => now()
            ]
        ]);
    }
}
