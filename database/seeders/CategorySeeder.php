<?php

namespace Database\Seeders;

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
                'name' => 'Sayuran Kaleng',
                'slug' => 'sayuran-kaleng',
                'description' => 'Koleksi sayuran segar dalam kemasan kaleng yang tahan lama',
                'is_active' => true,
            ],
            [
                'name' => 'Buah Kaleng',
                'slug' => 'buah-kaleng',
                'description' => 'Buah-buahan segar dan manis dalam kemasan kaleng',
                'is_active' => true,
            ],
            [
                'name' => 'Daging Kaleng',
                'slug' => 'daging-kaleng',
                'description' => 'Daging berkualitas tinggi yang diproses dan dikemas dalam kaleng',
                'is_active' => true,
            ],
            [
                'name' => 'Seafood Kaleng',
                'slug' => 'seafood-kaleng',
                'description' => 'Ikan dan makanan laut segar dalam kemasan kaleng',
                'is_active' => true,
            ],
            [
                'name' => 'Sup Kaleng',
                'slug' => 'sup-kaleng',
                'description' => 'Berbagai jenis sup siap saji dalam kemasan kaleng',
                'is_active' => true,
            ],
            [
                'name' => 'Pasta & Saus',
                'slug' => 'pasta-saus',
                'description' => 'Pasta dan saus siap pakai untuk hidangan praktis',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
