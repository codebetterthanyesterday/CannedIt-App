<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ShippingService
{
    protected $client;
    protected $apiKey;
    protected $baseUrl;
    protected $accountType;

    public function __construct()
    {
        $this->apiKey = config('rajaongkir.api_key');
        $this->baseUrl = config('rajaongkir.base_url');
        $this->accountType = config('rajaongkir.account_type');

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'timeout' => 30,
        ]);
    }

    /**
     * Get list of provinces
     *
     * @return array|null
     */
    public function getProvinces()
    {
        try {
            // Cache untuk 1 hari (data provinsi jarang berubah)
            return Cache::remember('rajaongkir_provinces', 86400, function () {
                $response = $this->client->get('/province');
                $body = json_decode($response->getBody(), true);

                if (isset($body['rajaongkir']['status']['code']) && $body['rajaongkir']['status']['code'] == 200) {
                    return $body['rajaongkir']['results'];
                }

                Log::error('RajaOngkir Get Provinces Error', [
                    'response' => $body
                ]);

                return null;
            });
        } catch (GuzzleException $e) {
            Log::error('RajaOngkir Get Provinces Exception', [
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            return null;
        }
    }

    /**
     * Get province by ID
     *
     * @param int $provinceId
     * @return array|null
     */
    public function getProvince($provinceId)
    {
        try {
            $response = $this->client->get('/province', [
                'query' => ['id' => $provinceId]
            ]);
            
            $body = json_decode($response->getBody(), true);

            if (isset($body['rajaongkir']['status']['code']) && $body['rajaongkir']['status']['code'] == 200) {
                return $body['rajaongkir']['results'];
            }

            return null;
        } catch (GuzzleException $e) {
            Log::error('RajaOngkir Get Province Exception', [
                'province_id' => $provinceId,
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get list of cities by province
     *
     * @param int|null $provinceId
     * @return array|null
     */
    public function getCities($provinceId = null)
    {
        try {
            $cacheKey = $provinceId ? "rajaongkir_cities_{$provinceId}" : 'rajaongkir_cities_all';
            
            // Cache untuk 1 hari
            return Cache::remember($cacheKey, 86400, function () use ($provinceId) {
                $query = $provinceId ? ['province' => $provinceId] : [];
                
                $response = $this->client->get('/city', [
                    'query' => $query
                ]);
                
                $body = json_decode($response->getBody(), true);

                if (isset($body['rajaongkir']['status']['code']) && $body['rajaongkir']['status']['code'] == 200) {
                    return $body['rajaongkir']['results'];
                }

                Log::error('RajaOngkir Get Cities Error', [
                    'province_id' => $provinceId,
                    'response' => $body
                ]);

                return null;
            });
        } catch (GuzzleException $e) {
            Log::error('RajaOngkir Get Cities Exception', [
                'province_id' => $provinceId,
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get city by ID
     *
     * @param int $cityId
     * @return array|null
     */
    public function getCity($cityId)
    {
        try {
            $response = $this->client->get('/city', [
                'query' => ['id' => $cityId]
            ]);
            
            $body = json_decode($response->getBody(), true);

            if (isset($body['rajaongkir']['status']['code']) && $body['rajaongkir']['status']['code'] == 200) {
                return $body['rajaongkir']['results'];
            }

            return null;
        } catch (GuzzleException $e) {
            Log::error('RajaOngkir Get City Exception', [
                'city_id' => $cityId,
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Calculate shipping cost
     *
     * @param int $originCityId
     * @param int $destinationCityId
     * @param int $weight (in grams)
     * @param string $courier
     * @return array|null
     */
    public function getCost($originCityId, $destinationCityId, $weight, $courier)
    {
        try {
            $response = $this->client->post('/cost', [
                'form_params' => [
                    'origin' => $originCityId,
                    'destination' => $destinationCityId,
                    'weight' => $weight,
                    'courier' => $courier
                ]
            ]);
            
            $body = json_decode($response->getBody(), true);

            if (isset($body['rajaongkir']['status']['code']) && $body['rajaongkir']['status']['code'] == 200) {
                return $body['rajaongkir']['results'];
            }

            Log::error('RajaOngkir Get Cost Error', [
                'origin' => $originCityId,
                'destination' => $destinationCityId,
                'weight' => $weight,
                'courier' => $courier,
                'response' => $body
            ]);

            return null;
        } catch (GuzzleException $e) {
            Log::error('RajaOngkir Get Cost Exception', [
                'origin' => $originCityId,
                'destination' => $destinationCityId,
                'weight' => $weight,
                'courier' => $courier,
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get multiple shipping costs from different couriers
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
            $couriers = config("rajaongkir.couriers.{$this->accountType}", ['jne', 'pos', 'tiki']);
        }

        $results = [];

        foreach ($couriers as $courier) {
            $cost = $this->getCost($originCityId, $destinationCityId, $weight, $courier);
            
            if ($cost) {
                $results[] = $cost;
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
        return config("rajaongkir.couriers.{$this->accountType}", ['jne', 'pos', 'tiki']);
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
