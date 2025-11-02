<?php

namespace App\Services;

/**
 * Mock Shipping Service for Development/Testing
 * Use this when RajaOngkir API is unavailable
 * 
 * To enable: Update ShippingController to use MockShippingService instead of ShippingService
 */
class MockShippingService
{
    protected $apiKey;
    protected $accountType;

    public function __construct()
    {
        $this->apiKey = config('rajaongkir.api_key');
        $this->accountType = config('rajaongkir.account_type', 'starter');
    }

    /**
     * Get list of provinces (mock data)
     *
     * @return array|null
     */
    public function getProvinces()
    {
        return [
            ['province_id' => '1', 'province' => 'Bali'],
            ['province_id' => '2', 'province' => 'Bangka Belitung'],
            ['province_id' => '3', 'province' => 'Banten'],
            ['province_id' => '4', 'province' => 'Bengkulu'],
            ['province_id' => '5', 'province' => 'DI Yogyakarta'],
            ['province_id' => '6', 'province' => 'DKI Jakarta'],
            ['province_id' => '7', 'province' => 'Gorontalo'],
            ['province_id' => '8', 'province' => 'Jambi'],
            ['province_id' => '9', 'province' => 'Jawa Barat'],
            ['province_id' => '10', 'province' => 'Jawa Tengah'],
            ['province_id' => '11', 'province' => 'Jawa Timur'],
            ['province_id' => '12', 'province' => 'Kalimantan Barat'],
            ['province_id' => '13', 'province' => 'Kalimantan Selatan'],
            ['province_id' => '14', 'province' => 'Kalimantan Tengah'],
            ['province_id' => '15', 'province' => 'Kalimantan Timur'],
            ['province_id' => '16', 'province' => 'Kalimantan Utara'],
            ['province_id' => '17', 'province' => 'Kepulauan Riau'],
            ['province_id' => '18', 'province' => 'Lampung'],
            ['province_id' => '19', 'province' => 'Maluku'],
            ['province_id' => '20', 'province' => 'Maluku Utara'],
            ['province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)'],
            ['province_id' => '22', 'province' => 'Nusa Tenggara Barat (NTB)'],
            ['province_id' => '23', 'province' => 'Nusa Tenggara Timur (NTT)'],
            ['province_id' => '24', 'province' => 'Papua'],
            ['province_id' => '25', 'province' => 'Papua Barat'],
            ['province_id' => '26', 'province' => 'Riau'],
            ['province_id' => '27', 'province' => 'Sulawesi Barat'],
            ['province_id' => '28', 'province' => 'Sulawesi Selatan'],
            ['province_id' => '29', 'province' => 'Sulawesi Tengah'],
            ['province_id' => '30', 'province' => 'Sulawesi Tenggara'],
            ['province_id' => '31', 'province' => 'Sulawesi Utara'],
            ['province_id' => '32', 'province' => 'Sumatera Barat'],
            ['province_id' => '33', 'province' => 'Sumatera Selatan'],
            ['province_id' => '34', 'province' => 'Sumatera Utara'],
        ];
    }

    /**
     * Get province by ID (mock data)
     *
     * @param int $provinceId
     * @return array|null
     */
    public function getProvince($provinceId)
    {
        $provinces = $this->getProvinces();
        foreach ($provinces as $province) {
            if ($province['province_id'] == $provinceId) {
                return [$province];
            }
        }
        return null;
    }

    /**
     * Get list of cities by province (mock data)
     *
     * @param int|null $provinceId
     * @return array|null
     */
    public function getCities($provinceId = null)
    {
        $allCities = [
            '6' => [ // DKI Jakarta
                ['city_id' => '151', 'province_id' => '6', 'province' => 'DKI Jakarta', 'type' => 'Kota', 'city_name' => 'Jakarta Selatan', 'postal_code' => '12230'],
                ['city_id' => '152', 'province_id' => '6', 'province' => 'DKI Jakarta', 'type' => 'Kota', 'city_name' => 'Jakarta Timur', 'postal_code' => '13330'],
                ['city_id' => '153', 'province_id' => '6', 'province' => 'DKI Jakarta', 'type' => 'Kota', 'city_name' => 'Jakarta Pusat', 'postal_code' => '10540'],
                ['city_id' => '154', 'province_id' => '6', 'province' => 'DKI Jakarta', 'type' => 'Kota', 'city_name' => 'Jakarta Utara', 'postal_code' => '14440'],
                ['city_id' => '155', 'province_id' => '6', 'province' => 'DKI Jakarta', 'type' => 'Kota', 'city_name' => 'Jakarta Barat', 'postal_code' => '11220'],
            ],
            '9' => [ // Jawa Barat
                ['city_id' => '23', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kota', 'city_name' => 'Bandung', 'postal_code' => '40111'],
                ['city_id' => '39', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kota', 'city_name' => 'Bekasi', 'postal_code' => '17837'],
                ['city_id' => '32', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kota', 'city_name' => 'Bogor', 'postal_code' => '16119'],
                ['city_id' => '107', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kota', 'city_name' => 'Depok', 'postal_code' => '16416'],
                ['city_id' => '89', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kota', 'city_name' => 'Cirebon', 'postal_code' => '45116'],
            ],
            '10' => [ // Jawa Tengah
                ['city_id' => '444', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kota', 'city_name' => 'Semarang', 'postal_code' => '50232'],
                ['city_id' => '455', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kota', 'city_name' => 'Solo', 'postal_code' => '57111'],
            ],
            '11' => [ // Jawa Timur
                ['city_id' => '445', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kota', 'city_name' => 'Surabaya', 'postal_code' => '60119'],
                ['city_id' => '317', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kota', 'city_name' => 'Malang', 'postal_code' => '65112'],
            ],
            '5' => [ // DI Yogyakarta
                ['city_id' => '501', 'province_id' => '5', 'province' => 'DI Yogyakarta', 'type' => 'Kota', 'city_name' => 'Yogyakarta', 'postal_code' => '55111'],
            ],
            '1' => [ // Bali
                ['city_id' => '114', 'province_id' => '1', 'province' => 'Bali', 'type' => 'Kota', 'city_name' => 'Denpasar', 'postal_code' => '80227'],
            ],
        ];

        if ($provinceId) {
            return $allCities[$provinceId] ?? [];
        }

        // Return all cities if no province specified
        $result = [];
        foreach ($allCities as $cities) {
            $result = array_merge($result, $cities);
        }
        return $result;
    }

    /**
     * Get city by ID (mock data)
     *
     * @param int $cityId
     * @return array|null
     */
    public function getCity($cityId)
    {
        $cities = $this->getCities();
        foreach ($cities as $city) {
            if ($city['city_id'] == $cityId) {
                return [$city];
            }
        }
        return null;
    }

    /**
     * Calculate shipping cost (mock data)
     *
     * @param int $originCityId
     * @param int $destinationCityId
     * @param int $weight (in grams)
     * @param string $courier
     * @return array|null
     */
    public function getCost($originCityId, $destinationCityId, $weight, $courier)
    {
        // Base cost per kg
        $baseRate = [
            'jne' => 9000,
            'pos' => 7500,
            'tiki' => 8500,
        ];

        $rate = $baseRate[$courier] ?? 9000;
        $weightInKg = ceil($weight / 1000);
        
        $costs = [
            'jne' => [
                'code' => 'jne',
                'name' => 'Jalur Nugraha Ekakurir (JNE)',
                'costs' => [
                    [
                        'service' => 'OKE',
                        'description' => 'Ongkos Kirim Ekonomis',
                        'cost' => [[
                            'value' => $rate * $weightInKg * 0.9,
                            'etd' => '4-5',
                            'note' => ''
                        ]]
                    ],
                    [
                        'service' => 'REG',
                        'description' => 'Layanan Reguler',
                        'cost' => [[
                            'value' => $rate * $weightInKg,
                            'etd' => '2-3',
                            'note' => ''
                        ]]
                    ],
                    [
                        'service' => 'YES',
                        'description' => 'Yakin Esok Sampai',
                        'cost' => [[
                            'value' => $rate * $weightInKg * 1.8,
                            'etd' => '1-1',
                            'note' => ''
                        ]]
                    ],
                ]
            ],
            'pos' => [
                'code' => 'pos',
                'name' => 'POS Indonesia (POS)',
                'costs' => [
                    [
                        'service' => 'Paket Kilat Khusus',
                        'description' => 'Paket Kilat Khusus',
                        'cost' => [[
                            'value' => $rate * $weightInKg * 0.85,
                            'etd' => '2-4',
                            'note' => ''
                        ]]
                    ],
                    [
                        'service' => 'Express Next Day',
                        'description' => 'Express Next Day',
                        'cost' => [[
                            'value' => $rate * $weightInKg * 1.5,
                            'etd' => '1-1',
                            'note' => ''
                        ]]
                    ],
                ]
            ],
            'tiki' => [
                'code' => 'tiki',
                'name' => 'Citra Van Titipan Kilat (TIKI)',
                'costs' => [
                    [
                        'service' => 'ECO',
                        'description' => 'Economy Service',
                        'cost' => [[
                            'value' => $rate * $weightInKg * 0.88,
                            'etd' => '4-6',
                            'note' => ''
                        ]]
                    ],
                    [
                        'service' => 'REG',
                        'description' => 'Regular Service',
                        'cost' => [[
                            'value' => $rate * $weightInKg,
                            'etd' => '3-4',
                            'note' => ''
                        ]]
                    ],
                    [
                        'service' => 'ONS',
                        'description' => 'Over Night Service',
                        'cost' => [[
                            'value' => $rate * $weightInKg * 1.7,
                            'etd' => '1-1',
                            'note' => ''
                        ]]
                    ],
                ]
            ],
        ];

        return [$costs[$courier] ?? []];
    }

    /**
     * Get multiple shipping costs from different couriers (mock data)
     *
     * @param int $originCityId
     * @param int $destinationCityId
     * @param int $weight
     * @param array $couriers
     * @return array
     */
    public function getMultipleCosts($originCityId, $destinationCityId, $weight, array $couriers = [])
    {
        if (empty($couriers)) {
            $couriers = $this->getSupportedCouriers();
        }

        $results = [];

        foreach ($couriers as $courier) {
            $cost = $this->getCost($originCityId, $destinationCityId, $weight, $courier);
            
            if ($cost && !empty($cost[0])) {
                $results[] = $cost[0];
            }
        }

        return $results;
    }

    /**
     * Get supported couriers based on account type
     *
     * @return array
     */
    public function getSupportedCouriers()
    {
        return ['jne', 'pos', 'tiki'];
    }

    /**
     * Get courier name
     *
     * @param string $code
     * @return string
     */
    public function getCourierName($code)
    {
        $couriers = [
            'jne' => 'JNE',
            'pos' => 'POS Indonesia',
            'tiki' => 'TIKI',
            'rpx' => 'RPX',
            'pcp' => 'PCP',
            'esl' => 'ESL',
            'pandu' => 'Pandu Logistics',
            'wahana' => 'Wahana',
            'sicepat' => 'SiCepat',
            'jnt' => 'J&T',
            'pahala' => 'Pahala',
            'sap' => 'SAP',
            'jet' => 'JET',
            'indah' => 'Indah Cargo',
            'dse' => 'DSE',
            'slis' => 'SLIS',
            'first' => 'First Logistics',
            'ncs' => 'NCS',
            'star' => 'Star Cargo',
        ];

        return $couriers[strtolower($code)] ?? strtoupper($code);
    }
}
