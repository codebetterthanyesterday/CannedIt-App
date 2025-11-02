<?php

namespace App\Http\Controllers;

use App\Services\MockShippingService as ShippingService;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    protected $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    /**
     * Get provinces
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProvinces()
    {
        $provinces = $this->shippingService->getProvinces();

        if ($provinces) {
            return response()->json([
                'success' => true,
                'data' => $provinces
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil data provinsi'
        ], 500);
    }

    /**
     * Get cities by province
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCities(Request $request)
    {
        $request->validate([
            'province_id' => 'required|numeric'
        ]);

        $cities = $this->shippingService->getCities($request->province_id);

        if ($cities) {
            return response()->json([
                'success' => true,
                'data' => $cities
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil data kota/kabupaten'
        ], 500);
    }

    /**
     * Calculate shipping cost
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculateCost(Request $request)
    {
        $request->validate([
            'destination_city_id' => 'required|numeric',
            'weight' => 'required|numeric|min:1',
            'courier' => 'required|string'
        ]);

        $originCityId = config('rajaongkir.origin_city_id');
        $cost = $this->shippingService->getCost(
            $originCityId,
            $request->destination_city_id,
            $request->weight,
            $request->courier
        );

        if ($cost) {
            return response()->json([
                'success' => true,
                'data' => $cost
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal menghitung ongkos kirim'
        ], 500);
    }

    /**
     * Get multiple shipping costs from different couriers
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMultipleCosts(Request $request)
    {
        $request->validate([
            'destination_city_id' => 'required|numeric',
            'weight' => 'required|numeric|min:1'
        ]);

        $originCityId = config('rajaongkir.origin_city_id');
        $couriers = $this->shippingService->getSupportedCouriers();
        
        $results = $this->shippingService->getMultipleCosts(
            $originCityId,
            $request->destination_city_id,
            $request->weight,
            $couriers
        );

        if ($results) {
            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal menghitung ongkos kirim'
        ], 500);
    }

    /**
     * Get supported couriers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCouriers()
    {
        $couriers = $this->shippingService->getSupportedCouriers();

        $courierList = [];
        foreach ($couriers as $code) {
            $courierList[] = [
                'code' => $code,
                'name' => $this->shippingService->getCourierName($code)
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $courierList
        ]);
    }
}
