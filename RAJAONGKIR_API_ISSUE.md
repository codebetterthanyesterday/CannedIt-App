# ⚠️ IMPORTANT: RajaOngkir API Update Required

## 🚨 Error 410 - API Endpoint Tidak Aktif

Saat testing, kami mendapat error:

```
410 Gone - Endpoint API ini sudah tidak aktif. 
Silakan migrasi ke platform baru
```

Ini berarti **RajaOngkir sudah migrasi ke platform/endpoint baru**.

---

## 🔧 Action Required

### Option 1: Check RajaOngkir Dashboard (RECOMMENDED)

1. Login ke https://rajaongkir.com/
2. Buka menu **"API"** atau **"Documentation"**
3. Cek apakah ada **notifikasi tentang migrasi**
4. Lihat **Base URL terbaru** yang harus digunakan
5. Kemungkinan mereka sudah pindah ke domain baru atau versioning API

### Option 2: Contact RajaOngkir Support

- Email: support@rajaongkir.com
- Tanyakan tentang:
  - New API endpoint/base URL
  - Migration guide
  - Apakah API key lama masih valid

### Option 3: Alternative Shipping APIs (if RajaOngkir sunset)

Jika RajaOngkir sudah tidak available, alternatif untuk shipping API Indonesia:

#### 1. **Shipper.id** 
- Website: https://shipper.id/
- Features: Multi-courier, pickup, tracking
- Pricing: Pay-as-you-go
- Integration: REST API

#### 2. **Biteship**
- Website: https://biteship.com/
- Features: 30+ couriers, real-time tracking
- Pricing: Free tier available
- Integration: REST API (mirip RajaOngkir)

#### 3. **ShipDeo** (formerly SendHero)
- Website: https://shipdeo.com/
- Features: JNE, TIKI, SiCepat, J&T integration
- Pricing: Free for developers

#### 4. **Direct Courier Integration**
- JNE API: https://www.jne.co.id/id/beranda/API
- TIKI API: Contact TIKI directly
- SiCepat API: https://api.sicepat.com/

---

## 🛠️ Temporary Workaround (For Development)

Jika Anda butuh test aplikasi sekarang sambil menunggu fix RajaOngkir, saya bisa buatkan:

### Mock Shipping Service

File: `app/Services/MockShippingService.php`

```php
<?php

namespace App\Services;

class MockShippingService
{
    public function getProvinces()
    {
        return [
            ['province_id' => '6', 'province' => 'DKI Jakarta'],
            ['province_id' => '9', 'province' => 'Jawa Barat'],
            ['province_id' => '10', 'province' => 'Jawa Tengah'],
            ['province_id' => '11', 'province' => 'Jawa Timur'],
            ['province_id' => '1', 'province' => 'Bali'],
        ];
    }

    public function getCities($provinceId)
    {
        $cities = [
            '6' => [
                ['city_id' => '151', 'province_id' => '6', 'type' => 'Kota', 'city_name' => 'Jakarta Selatan', 'postal_code' => '12230'],
                ['city_id' => '152', 'province_id' => '6', 'type' => 'Kota', 'city_name' => 'Jakarta Timur', 'postal_code' => '13330'],
                ['city_id' => '153', 'province_id' => '6', 'type' => 'Kota', 'city_name' => 'Jakarta Pusat', 'postal_code' => '10540'],
            ],
            '9' => [
                ['city_id' => '23', 'province_id' => '9', 'type' => 'Kota', 'city_name' => 'Bandung', 'postal_code' => '40111'],
                ['city_id' => '39', 'province_id' => '9', 'type' => 'Kota', 'city_name' => 'Bekasi', 'postal_code' => '17837'],
            ],
        ];

        return $cities[$provinceId] ?? [];
    }

    public function getCost($origin, $destination, $weight, $courier)
    {
        $costs = [
            'jne' => [
                'code' => 'jne',
                'name' => 'Jalur Nugraha Ekakurir (JNE)',
                'costs' => [
                    ['service' => 'OKE', 'description' => 'Ongkos Kirim Ekonomis', 'cost' => [['value' => 18000, 'etd' => '4-5', 'note' => '']]],
                    ['service' => 'REG', 'description' => 'Layanan Reguler', 'cost' => [['value' => 20000, 'etd' => '2-3', 'note' => '']]],
                    ['service' => 'YES', 'description' => 'Yakin Esok Sampai', 'cost' => [['value' => 35000, 'etd' => '1-1', 'note' => '']]],
                ]
            ],
            'pos' => [
                'code' => 'pos',
                'name' => 'POS Indonesia (POS)',
                'costs' => [
                    ['service' => 'Paket Kilat Khusus', 'description' => 'Paket Kilat Khusus', 'cost' => [['value' => 15000, 'etd' => '2-4', 'note' => '']]],
                    ['service' => 'Express Next Day', 'description' => 'Express Next Day', 'cost' => [['value' => 22000, 'etd' => '1-1', 'note' => '']]],
                ]
            ],
            'tiki' => [
                'code' => 'tiki',
                'name' => 'Citra Van Titipan Kilat (TIKI)',
                'costs' => [
                    ['service' => 'ECO', 'description' => 'Economy Service', 'cost' => [['value' => 17000, 'etd' => '4-6', 'note' => '']]],
                    ['service' => 'REG', 'description' => 'Regular Service', 'cost' => [['value' => 21000, 'etd' => '3-4', 'note' => '']]],
                    ['service' => 'ONS', 'description' => 'Over Night Service', 'cost' => [['value' => 32000, 'etd' => '1-1', 'note' => '']]],
                ]
            ],
        ];

        return [$costs[$courier] ?? []];
    }

    public function getMultipleCosts($origin, $destination, $weight, $couriers)
    {
        $results = [];
        foreach ($couriers as $courier) {
            $cost = $this->getCost($origin, $destination, $weight, $courier);
            if (!empty($cost[0])) {
                $results[] = $cost[0];
            }
        }
        return $results;
    }

    public function getSupportedCouriers()
    {
        return ['jne', 'pos', 'tiki'];
    }

    public function getCourierName($code)
    {
        $couriers = [
            'jne' => 'JNE',
            'pos' => 'POS Indonesia',
            'tiki' => 'TIKI',
        ];
        return $couriers[strtolower($code)] ?? strtoupper($code);
    }
}
```

**Cara pakai:**
1. Rename `ShippingService.php` jadi `ShippingService.php.backup`
2. Rename `MockShippingService.php` jadi `ShippingService.php`
3. Test aplikasi dengan data static
4. Setelah RajaOngkir issue solved, kembalikan ke service asli

---

## 📝 Update Checklist

### When RajaOngkir Issue Resolved:

- [ ] Update `RAJAONGKIR_API_KEY` di `.env` (jika ada key baru)
- [ ] Update `base_url` di `config/rajaongkir.php`
- [ ] Test `/shipping/provinces` endpoint
- [ ] Test `/shipping/cities` endpoint  
- [ ] Test `/shipping/costs` endpoint
- [ ] Update dokumentasi dengan API version baru

---

## 💡 Current Status

**Integration Code:** ✅ Complete & Ready
**API Endpoint:** ❌ RajaOngkir API deprecated (Error 410)
**Frontend:** ✅ Fully functional (waiting for API)
**Database:** ✅ All fields ready

**Next Action:** Check RajaOngkir dashboard for migration guide or use alternative shipping API.

---

## 🔗 Useful Links

- RajaOngkir: https://rajaongkir.com/
- Shipper.id: https://shipper.id/
- Biteship: https://biteship.com/
- ShipDeo: https://shipdeo.com/

---

**Note:** Kode integrasi yang sudah dibuat tetap bisa dipakai! Cuma perlu update base URL atau ganti ke shipping API alternatif. Struktur response biasanya mirip-mirip antar shipping API Indonesia.
