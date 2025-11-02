# RajaOngkir Shipping API Integration

Dokumentasi integrasi RajaOngkir Shipping API untuk menghitung ongkos kirim secara otomatis.

## 📋 Fitur

- ✅ Mendapatkan daftar provinsi dan kota/kabupaten
- ✅ Menghitung ongkos kirim dari berbagai kurir (JNE, POS, TIKI, dll)
- ✅ Support multiple couriers
- ✅ Caching data provinsi dan kota
- ✅ AJAX integration untuk real-time calculation

## 🔑 Setup

### 1. Daftar RajaOngkir

1. Buka https://rajaongkir.com/
2. Klik "Daftar" dan buat akun baru
3. Pilih paket:
   - **Starter** (GRATIS): Mendukung 3 kurir (JNE, POS, TIKI)
   - **Basic** (Rp 25.000/bulan): Mendukung 19 kurir
   - **Pro** (Rp 150.000/bulan): Mendukung 19 kurir + fitur tracking

4. Setelah login, buka menu **"Akun"** untuk mendapatkan API Key

### 2. Konfigurasi .env

Tambahkan konfigurasi berikut ke file `.env`:

```env
# RajaOngkir Shipping API
RAJAONGKIR_API_KEY=your_api_key_here
RAJAONGKIR_ACCOUNT_TYPE=starter
RAJAONGKIR_ORIGIN_CITY_ID=154
```

**Keterangan:**
- `RAJAONGKIR_API_KEY`: API Key dari dashboard RajaOngkir
- `RAJAONGKIR_ACCOUNT_TYPE`: Tipe akun (starter/basic/pro)
- `RAJAONGKIR_ORIGIN_CITY_ID`: ID kota asal pengiriman (default: 154 untuk Jakarta Pusat)

### 3. Cari ID Kota Asal

Untuk mendapatkan ID kota gudang/toko Anda:

**Cara 1: Via Browser**

Buka https://api.rajaongkir.com/starter/city?key=YOUR_API_KEY

**Cara 2: Via Code**

```php
use App\Services\ShippingService;

$shipping = new ShippingService();
$cities = $shipping->getCities();

// Cari kota Anda
foreach ($cities as $city) {
    if (str_contains(strtolower($city['city_name']), 'jakarta')) {
        echo $city['city_id'] . ' - ' . $city['city_name'] . "\n";
    }
}
```

**Contoh ID Kota:**
- Jakarta Pusat: 154
- Jakarta Selatan: 151
- Bandung: 23
- Surabaya: 444
- Yogyakarta: 501

### 4. Clear Cache

Setelah mengubah konfigurasi:

```bash
php artisan config:clear
php artisan cache:clear
```

## 📡 API Endpoints

### 1. Get Provinces

**Endpoint:** `GET /shipping/provinces`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "province_id": "1",
      "province": "Bali"
    },
    {
      "province_id": "2",
      "province": "Bangka Belitung"
    }
  ]
}
```

### 2. Get Cities by Province

**Endpoint:** `GET /shipping/cities?province_id=9`

**Parameters:**
- `province_id` (required): ID provinsi

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "city_id": "151",
      "province_id": "6",
      "province": "DKI Jakarta",
      "type": "Kota",
      "city_name": "Jakarta Selatan",
      "postal_code": "12230"
    }
  ]
}
```

### 3. Calculate Single Shipping Cost

**Endpoint:** `POST /shipping/calculate`

**Parameters:**
- `destination_city_id` (required): ID kota tujuan
- `weight` (required): Berat dalam gram
- `courier` (required): Kode kurir (jne/pos/tiki)

**Request:**
```json
{
  "destination_city_id": 151,
  "weight": 1000,
  "courier": "jne"
}
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "code": "jne",
      "name": "Jalur Nugraha Ekakurir (JNE)",
      "costs": [
        {
          "service": "OKE",
          "description": "Ongkos Kirim Ekonomis",
          "cost": [
            {
              "value": 18000,
              "etd": "4-5",
              "note": ""
            }
          ]
        },
        {
          "service": "REG",
          "description": "Layanan Reguler",
          "cost": [
            {
              "value": 20000,
              "etd": "2-3",
              "note": ""
            }
          ]
        }
      ]
    }
  ]
}
```

### 4. Get Multiple Shipping Costs

**Endpoint:** `POST /shipping/costs`

**Parameters:**
- `destination_city_id` (required): ID kota tujuan
- `weight` (required): Berat dalam gram

**Request:**
```json
{
  "destination_city_id": 151,
  "weight": 1000
}
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "code": "jne",
      "name": "Jalur Nugraha Ekakurir (JNE)",
      "costs": [...]
    },
    {
      "code": "pos",
      "name": "POS Indonesia (POS)",
      "costs": [...]
    },
    {
      "code": "tiki",
      "name": "Citra Van Titipan Kilat (TIKI)",
      "costs": [...]
    }
  ]
}
```

### 5. Get Supported Couriers

**Endpoint:** `GET /shipping/couriers`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "code": "jne",
      "name": "JNE"
    },
    {
      "code": "pos",
      "name": "POS Indonesia"
    },
    {
      "code": "tiki",
      "name": "TIKI"
    }
  ]
}
```

## 💻 Penggunaan di Frontend

### Contoh AJAX Call untuk Menghitung Ongkir

```javascript
// Ketika user memilih kota tujuan dan kurir
async function calculateShipping(destinationCityId, weight, courier) {
    try {
        const response = await fetch('/shipping/calculate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                destination_city_id: destinationCityId,
                weight: weight,
                courier: courier
            })
        });

        const result = await response.json();
        
        if (result.success) {
            const costs = result.data[0].costs;
            
            // Display shipping options
            costs.forEach(option => {
                console.log(`${option.service}: Rp ${option.cost[0].value} (${option.cost[0].etd} hari)`);
            });
        }
    } catch (error) {
        console.error('Error calculating shipping:', error);
    }
}

// Contoh penggunaan
calculateShipping(151, 1000, 'jne'); // Jakarta Selatan, 1kg, JNE
```

### Load Provinsi dan Kota

```javascript
// Load provinces
async function loadProvinces() {
    const response = await fetch('/shipping/provinces');
    const result = await response.json();
    
    if (result.success) {
        const select = document.getElementById('province');
        result.data.forEach(province => {
            const option = new Option(province.province, province.province_id);
            select.add(option);
        });
    }
}

// Load cities when province selected
async function loadCities(provinceId) {
    const response = await fetch(`/shipping/cities?province_id=${provinceId}`);
    const result = await response.json();
    
    if (result.success) {
        const select = document.getElementById('city');
        select.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
        
        result.data.forEach(city => {
            const cityName = `${city.type} ${city.city_name}`;
            const option = new Option(cityName, city.city_id);
            select.add(option);
        });
    }
}
```

## 🔧 Penggunaan di Backend

### Contoh di Controller

```php
use App\Services\ShippingService;

class CheckoutController extends Controller
{
    protected $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    public function calculateShipping(Request $request)
    {
        // Hitung total berat dari cart items
        $totalWeight = 0;
        foreach ($cartItems as $item) {
            $totalWeight += $item->product->weight * $item->quantity;
        }

        // Get shipping cost
        $costs = $this->shippingService->getCost(
            config('rajaongkir.origin_city_id'), // Kota asal
            $request->destination_city_id,        // Kota tujuan
            $totalWeight,                         // Berat total (gram)
            $request->courier                     // Kurir (jne/pos/tiki)
        );

        return view('checkout', compact('costs'));
    }
}
```

### Menyimpan Data Pengiriman di Order

Tambahkan field di migration `orders` table:

```php
$table->string('shipping_province')->nullable();
$table->string('shipping_city')->nullable();
$table->integer('shipping_city_id')->nullable();
$table->string('shipping_courier')->nullable(); // jne, pos, tiki
$table->string('shipping_service')->nullable(); // REG, OKE, YES
$table->integer('shipping_cost')->default(0);
$table->string('shipping_etd')->nullable(); // 2-3 hari
```

## 📊 Supported Couriers

### Paket Starter (GRATIS)
- **JNE** - Jalur Nugraha Ekakurir
- **POS** - POS Indonesia  
- **TIKI** - Titipan Kilat

### Paket Basic/Pro
Semua kurir di atas, plus:
- **RPX** - RPX Holding
- **PCP** - Priority Cargo and Package
- **ESL** - Eka Sari Lorena
- **Pandu** - Pandu Logistics
- **Wahana** - Wahana Prestasi Logistik
- **SiCepat** - SiCepat Express
- **J&T** - J&T Express
- **Pahala** - Pahala Kencana Express
- **SAP** - SAP Express
- **JET** - JET Express
- **Indah Cargo** - Indah Logistic Cargo
- **DSE** - 21 Express
- **SLIS** - Solusi Ekspres
- **First** - First Logistics
- **NCS** - Nusantara Card Semesta
- **Star** - Star Cargo

## 🎯 Testing

### Test di Postman/Thunder Client

**1. Test Get Provinces:**
```
GET http://localhost/shipping/provinces
```

**2. Test Get Cities:**
```
GET http://localhost/shipping/cities?province_id=6
```

**3. Test Calculate Cost:**
```
POST http://localhost/shipping/calculate
Content-Type: application/json

{
    "destination_city_id": 151,
    "weight": 1000,
    "courier": "jne"
}
```

### Test di Browser Console

```javascript
// Test dengan fetch
fetch('/shipping/provinces')
    .then(r => r.json())
    .then(data => console.log(data));

fetch('/shipping/costs', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        destination_city_id: 151,
        weight: 1000
    })
}).then(r => r.json()).then(data => console.log(data));
```

## 📝 Notes

1. **Caching**: Data provinsi dan kota di-cache selama 24 jam untuk performa
2. **Weight**: Berat harus dalam satuan gram (1 kg = 1000 gram)
3. **ETD**: Estimasi waktu pengiriman dalam format "2-3" (hari)
4. **Origin**: Kota asal diambil dari config `rajaongkir.origin_city_id`
5. **Rate Limit**: API Starter terbatas 1000 requests/bulan

## 🐛 Troubleshooting

### Error: "Gagal mengambil data provinsi"
- Cek API Key di `.env` sudah benar
- Pastikan account type sesuai (starter/basic/pro)
- Cek koneksi internet

### Error: "Gagal menghitung ongkos kirim"
- Pastikan city_id valid
- Cek courier code (harus lowercase: jne, pos, tiki)
- Pastikan weight > 0
- Cek log di `storage/logs/laravel.log`

### Cache Issue
Jika data tidak update setelah perubahan:
```bash
php artisan cache:clear
```

## 📚 Dokumentasi Resmi

- RajaOngkir Docs: https://rajaongkir.com/dokumentasi
- API Starter: https://rajaongkir.com/dokumentasi/starter
- API Basic: https://rajaongkir.com/dokumentasi/basic
- API Pro: https://rajaongkir.com/dokumentasi/pro

---

✅ Setup selesai! Aplikasi sekarang bisa menghitung ongkir secara otomatis.
