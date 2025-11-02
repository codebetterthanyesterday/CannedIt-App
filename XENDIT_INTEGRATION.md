# Integrasi Xendit Payment Gateway

## Persiapan

### 1. Daftar Akun Xendit Sandbox

1. Kunjungi [https://dashboard.xendit.co/register](https://dashboard.xendit.co/register)
2. Daftar akun baru untuk testing
3. Verifikasi email Anda
4. Login ke dashboard Xendit

### 2. Dapatkan API Keys

1. Login ke [Xendit Dashboard](https://dashboard.xendit.co/)
2. Pastikan Anda dalam mode **Sandbox** (Test Mode)
3. Pergi ke **Settings** → **API Keys** atau **Developers** → **API Keys**
4. Copy **Secret Key** dan **Public Key**

### 3. Setup Environment Variables

Tambahkan konfigurasi berikut ke file `.env`:

```env
XENDIT_SECRET_KEY=xnd_development_XXXXXXXXXXXXXXXXXXXXXX
XENDIT_PUBLIC_KEY=xnd_public_development_XXXXXXXXXXXXXXXXXXXXXX
XENDIT_CALLBACK_TOKEN=your_random_secure_token_123456
XENDIT_IS_PRODUCTION=false
```

**Generate Callback Token:**
```bash
php artisan tinker
>>> Str::random(32)
```

### 4. Install Xendit PHP SDK

Xendit PHP SDK sudah terinstall. Jika perlu install ulang:
```bash
composer require xendit/xendit-php
composer dump-autoload
php artisan config:clear
```

**Versi yang digunakan:** Xendit PHP SDK v7.0.0

**Catatan:** SDK v7 menggunakan API baru dengan namespace yang berbeda:
- `Xendit\Configuration::setXenditKey()`
- `Xendit\Invoice\InvoiceApi` untuk invoice operations
- `Xendit\Invoice\CreateInvoiceRequest` untuk create invoice

### 5. Setup Webhook URL

1. Di Xendit Dashboard, pergi ke **Settings** → **Webhooks**
2. Tambahkan webhook URL:
   ```
   https://your-domain.com/xendit/webhook
   ```
3. Pilih events yang ingin di-subscribe:
   - `invoice.paid`
   - `invoice.expired`
4. Set verification token sama dengan `XENDIT_CALLBACK_TOKEN` di `.env`

**Untuk Local Development:**
- Gunakan [ngrok](https://ngrok.com/) untuk expose local server:
  ```bash
  ngrok http 80
  ```
- Gunakan URL dari ngrok untuk webhook URL

## Cara Kerja Payment Flow

### 1. Checkout → Create Order
- User melakukan checkout dari keranjang
- Sistem membuat order dengan status `pending`
- Sistem otomatis membuat Xendit invoice menggunakan `XenditService`

### 2. Redirect ke Xendit Payment Page
- User di-redirect ke halaman pembayaran Xendit
- User memilih metode pembayaran (Bank Transfer, E-Wallet, Credit Card, dll)
- User menyelesaikan pembayaran

### 3. Webhook Callback
- Xendit mengirim notifikasi ke webhook URL
- Sistem memverifikasi callback token
- Sistem update status order:
  - Status: `pending` → `processing`
  - Payment Status: `pending` → `paid`
  - Menyimpan `xendit_paid_at`, `payment_channel`, `payment_reference`

### 4. Payment Success/Failed Pages
- **Success**: User di-redirect ke `/payment/{order}/success`
- **Failed**: User di-redirect ke `/payment/{order}/failed`

## Testing Payment di Sandbox

### Test Credit Card
- Card Number: `4000000000000002`
- CVV: `123`
- Exp Date: Any future date

### Test Bank Transfer (Virtual Account)
- BCA: Gunakan VA number yang digenerate
- Mandiri: Gunakan VA number yang digenerate
- BNI: Gunakan VA number yang digenerate

**Simulasi Pembayaran:**
1. Copy nomor VA yang digenerate
2. Pergi ke Xendit Dashboard → **Transactions** → **Virtual Accounts**
3. Cari VA number Anda
4. Klik **Simulate Payment**

### Test E-Wallet
- OVO, DANA, LinkAja: Akan menampilkan payment instruction
- Gunakan phone number test: `081234567890`

## Troubleshooting

### Class "Xendit\Xendit" not found
**Solusi:**
```bash
composer require xendit/xendit-php
composer dump-autoload
php artisan config:clear
```

Pastikan `xendit/xendit-php` ada di `composer.json` dan folder `vendor/xendit` exists.

### Invoice Tidak Terbuat
- Cek `XENDIT_SECRET_KEY` di `.env` sudah benar
- Cek log Laravel: `storage/logs/laravel.log`
- Pastikan Xendit dalam mode Sandbox

### Webhook Tidak Diterima
- Pastikan webhook URL sudah di-setup di Xendit Dashboard
- Cek `XENDIT_CALLBACK_TOKEN` sama dengan token di Xendit
- Untuk local dev, pastikan ngrok masih running
- Cek log webhook di Xendit Dashboard

### Payment Status Tidak Update
- Cek webhook logs di Xendit Dashboard
- Pastikan route `/xendit/webhook` tidak memerlukan authentication
- Cek database order table untuk field Xendit

## API Methods

### XenditService Methods

```php
// Create invoice for payment
$result = $xenditService->createInvoice($order);
// Returns: ['success' => true, 'invoice_id' => '...', 'invoice_url' => '...', 'expiry_date' => '...']

// Get invoice status
$result = $xenditService->getInvoice($invoiceId);
// Returns: ['success' => true, 'data' => [...]]

// Create virtual account
$result = $xenditService->createVirtualAccount($order, 'BCA');
// Returns: ['success' => true, 'va_number' => '...', 'bank_code' => '...']

// Verify webhook callback
$isValid = $xenditService->verifyCallback($callbackToken);
// Returns: true/false
```

## Production Deployment

### Before Go Live:
1. Daftar akun Xendit Production
2. Lengkapi verifikasi bisnis di Xendit
3. Update `.env`:
   ```env
   XENDIT_SECRET_KEY=xnd_production_XXXXXXXXXXXXXXXXXXXXXX
   XENDIT_PUBLIC_KEY=xnd_public_production_XXXXXXXXXXXXXXXXXXXXXX
   XENDIT_IS_PRODUCTION=true
   ```
4. Update webhook URL ke production domain
5. Test semua payment methods
6. Setup monitoring dan alert

## Referensi
- [Xendit API Documentation](https://developers.xendit.co/api-reference/)
- [Xendit PHP SDK](https://github.com/xendit/xendit-php)
- [Payment Methods](https://docs.xendit.co/docs/payment-methods)
