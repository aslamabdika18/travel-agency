<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Adventure',
                'description' => 'Paket wisata petualangan dengan aktivitas menantang seperti diving, snorkeling, dan eksplorasi alam'
            ],
            [
                'name' => 'Cultural',
                'description' => 'Paket wisata budaya untuk menjelajahi tradisi lokal, kuliner khas, dan warisan maritim'
            ],
            [
                'name' => 'Eco-Tourism',
                'description' => 'Paket wisata ramah lingkungan dengan fokus konservasi alam dan pemberdayaan masyarakat lokal'
            ],
            [
                'name' => 'Photography',
                'description' => 'Paket wisata khusus fotografi dengan lokasi terbaik dan bimbingan fotografer profesional'
            ],
            [
                'name' => 'Beach & Island',
                'description' => 'Paket wisata pantai dan pulau dengan aktivitas santai, island hopping, dan menikmati keindahan laut'
            ],
            [
                'name' => 'Culinary',
                'description' => 'Paket wisata kuliner untuk menikmati makanan khas daerah dan belajar teknik memasak tradisional'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}