<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Sayuran Kaleng
            [
                'category_id' => 1,
                'name' => 'Green Giant Sweet Corn',
                'slug' => 'green-giant-sweet-corn',
                'sku' => 'GG-CORN-340',
                'description' => 'Jagung manis pilihan berkualitas tinggi, dipetik pada saat matang sempurna dan langsung diproses untuk mempertahankan rasa dan nutrisi. Cocok untuk salad, sup, atau dimakan langsung.',
                'short_description' => 'Jagung manis premium dalam kaleng, segar dan bergizi',
                'price' => 25000,
                'stock_quantity' => 100,
                'brand' => 'Green Giant',
                'weight' => '340g',
                'expiry_date' => '2026-12-31',
                'manufacture_date' => '2024-01-15',
                'ingredients' => 'Jagung manis, air, garam',
                'nutritional_info' => 'Per 100g: Kalori 86, Protein 3.2g, Karbohidrat 19g, Lemak 1.2g',
                'is_featured' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Del Monte Green Beans',
                'slug' => 'del-monte-green-beans',
                'sku' => 'DM-BEAN-400',
                'description' => 'Buncis hijau segar yang dipetik langsung dari kebun dan diproses dengan teknologi modern untuk mempertahankan warna, tekstur, dan nutrisi.',
                'short_description' => 'Buncis hijau segar dalam kaleng, kaya vitamin',
                'price' => 22000,
                'stock_quantity' => 80,
                'brand' => 'Del Monte',
                'weight' => '400g',
                'expiry_date' => '2026-11-30',
                'manufacture_date' => '2024-02-10',
                'ingredients' => 'Buncis hijau, air, garam',
                'nutritional_info' => 'Per 100g: Kalori 31, Protein 1.8g, Karbohidrat 7g, Lemak 0.1g',
                'is_featured' => false,
            ],
            
            // Buah Kaleng
            [
                'category_id' => 2,
                'name' => 'Dole Pineapple Chunks',
                'slug' => 'dole-pineapple-chunks',
                'sku' => 'DOLE-PINE-567',
                'description' => 'Potongan nanas segar dalam sirup ringan, dipetik dari perkebunan terbaik. Sempurna untuk desert, cake, atau dimakan langsung sebagai camilan sehat.',
                'short_description' => 'Potongan nanas manis dalam sirup, segar dan alami',
                'price' => 35000,
                'sale_price' => 30000,
                'stock_quantity' => 120,
                'brand' => 'Dole',
                'weight' => '567g',
                'expiry_date' => '2027-03-15',
                'manufacture_date' => '2024-03-20',
                'ingredients' => 'Nanas, sirup (air, gula), asam sitrat',
                'nutritional_info' => 'Per 100g: Kalori 60, Protein 0.4g, Karbohidrat 15g, Lemak 0.1g',
                'is_featured' => true,
            ],
            [
                'category_id' => 2,
                'name' => 'Libby\'s Peach Halves',
                'slug' => 'libbys-peach-halves',
                'sku' => 'LIB-PEACH-825',
                'description' => 'Belahan buah persik manis dalam sirup ringan, dengan tekstur lembut dan rasa yang segar. Ideal untuk dessert atau sebagai topping.',
                'short_description' => 'Belahan buah persik manis dalam sirup',
                'price' => 40000,
                'stock_quantity' => 60,
                'brand' => 'Libby\'s',
                'weight' => '825g',
                'expiry_date' => '2026-10-20',
                'manufacture_date' => '2024-01-05',
                'ingredients' => 'Persik, sirup (air, gula), asam askorbat',
                'nutritional_info' => 'Per 100g: Kalori 68, Protein 0.6g, Karbohidrat 17g, Lemak 0.1g',
                'is_featured' => false,
            ],
            
            // Daging Kaleng
            [
                'category_id' => 3,
                'name' => 'Spam Classic',
                'slug' => 'spam-classic',
                'sku' => 'SPAM-CLAS-340',
                'description' => 'Daging babi olahan premium yang sudah dimasak dan siap santap. Dapat dimakan langsung, digoreng, atau dijadikan bahan masakan lainnya.',
                'short_description' => 'Daging babi olahan premium siap santap',
                'price' => 65000,
                'stock_quantity' => 45,
                'brand' => 'Spam',
                'weight' => '340g',
                'expiry_date' => '2027-06-30',
                'manufacture_date' => '2024-04-12',
                'ingredients' => 'Daging babi, ham, garam, air, gula, sodium nitrit',
                'nutritional_info' => 'Per 100g: Kalori 310, Protein 13g, Karbohidrat 2g, Lemak 27g',
                'is_featured' => true,
            ],
            [
                'category_id' => 3,
                'name' => 'Corned Beef Pronas',
                'slug' => 'corned-beef-pronas',
                'sku' => 'PRO-CORN-198',
                'description' => 'Kornet sapi berkualitas tinggi dengan cita rasa gurih khas Indonesia. Cocok untuk nasi goreng, sandwich, atau dimakan dengan nasi hangat.',
                'short_description' => 'Kornet sapi berkualitas dengan rasa gurih khas',
                'price' => 45000,
                'stock_quantity' => 75,
                'brand' => 'Pronas',
                'weight' => '198g',
                'expiry_date' => '2026-09-15',
                'manufacture_date' => '2024-02-28',
                'ingredients' => 'Daging sapi, air, garam, pengawet',
                'nutritional_info' => 'Per 100g: Kalori 250, Protein 25g, Karbohidrat 1g, Lemak 16g',
                'is_featured' => false,
            ],
            
            // Seafood Kaleng
            [
                'category_id' => 4,
                'name' => 'Ayam Brand Tuna in Water',
                'slug' => 'ayam-brand-tuna-in-water',
                'sku' => 'AB-TUNA-185',
                'description' => 'Tuna chunk dalam air, tanpa minyak tambahan sehingga lebih sehat. Kaya protein dan omega-3, perfect untuk salad atau sandwich.',
                'short_description' => 'Tuna chunk dalam air, rendah lemak dan sehat',
                'price' => 28000,
                'stock_quantity' => 150,
                'brand' => 'Ayam Brand',
                'weight' => '185g',
                'expiry_date' => '2027-01-20',
                'manufacture_date' => '2024-05-10',
                'ingredients' => 'Tuna, air, garam',
                'nutritional_info' => 'Per 100g: Kalori 116, Protein 25g, Karbohidrat 0g, Lemak 1g',
                'is_featured' => true,
            ],
            [
                'category_id' => 4,
                'name' => 'Sardines in Tomato Sauce',
                'slug' => 'sardines-in-tomato-sauce',
                'sku' => 'ABC-SARD-155',
                'description' => 'Sarden segar dalam saus tomat yang lezat. Kaya kalsium dan omega-3, cocok dimakan dengan nasi atau roti.',
                'short_description' => 'Sarden dalam saus tomat yang lezat dan bergizi',
                'price' => 18000,
                'stock_quantity' => 200,
                'brand' => 'ABC',
                'weight' => '155g',
                'expiry_date' => '2026-08-30',
                'manufacture_date' => '2024-03-15',
                'ingredients' => 'Sarden, saus tomat, garam, gula, rempah',
                'nutritional_info' => 'Per 100g: Kalori 135, Protein 18g, Karbohidrat 3g, Lemak 6g',
                'is_featured' => false,
            ],
            
            // Sup Kaleng
            [
                'category_id' => 5,
                'name' => 'Campbell\'s Chicken Soup',
                'slug' => 'campbells-chicken-soup',
                'sku' => 'CAM-SOUP-305',
                'description' => 'Sup ayam klasik yang hangat dan mengenyangkan. Dibuat dari kaldu ayam asli dengan potongan daging ayam dan sayuran.',
                'short_description' => 'Sup ayam hangat dengan kaldu asli dan sayuran',
                'price' => 32000,
                'stock_quantity' => 90,
                'brand' => 'Campbell\'s',
                'weight' => '305g',
                'expiry_date' => '2026-12-15',
                'manufacture_date' => '2024-04-20',
                'ingredients' => 'Kaldu ayam, daging ayam, wortel, seledri, bawang, tepung',
                'nutritional_info' => 'Per 100g: Kalori 45, Protein 3g, Karbohidrat 5g, Lemak 1.5g',
                'is_featured' => false,
            ],
            
            // Pasta & Saus
            [
                'category_id' => 6,
                'name' => 'Prego Traditional Pasta Sauce',
                'slug' => 'prego-traditional-pasta-sauce',
                'sku' => 'PRE-PAST-680',
                'description' => 'Saus pasta tradisional Italia dengan tomat segar, herbs, dan rempah pilihan. Siap pakai untuk berbagai jenis pasta.',
                'short_description' => 'Saus pasta tradisional Italia siap pakai',
                'price' => 38000,
                'stock_quantity' => 70,
                'brand' => 'Prego',
                'weight' => '680g',
                'expiry_date' => '2026-11-10',
                'manufacture_date' => '2024-01-30',
                'ingredients' => 'Tomat, bawang, bawang putih, olive oil, herbs, garam',
                'nutritional_info' => 'Per 100g: Kalori 70, Protein 2g, Karbohidrat 12g, Lemak 2g',
                'is_featured' => true,
            ],
        ];

        foreach ($products as $product) {
            \App\Models\Product::create($product);
        }
    }
}
