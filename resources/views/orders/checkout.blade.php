@extends('layouts.app')

@section('title', 'Checkout - CannedIt')
@section('description', 'Selesaikan pembelian produk makanan kaleng dengan proses checkout yang aman dan mudah.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <a href="{{ route('home') }}" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-home"></i>
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                        <a href="{{ route('cart.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Keranjang</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                        <span class="ml-4 text-sm font-medium text-gray-900">Checkout</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start xl:gap-x-16">
        <!-- Left Column - Checkout Form -->
        <div class="lg:col-span-7">
            <form id="checkout-form" action="{{ route('orders.store') }}" method="POST">
                @csrf
                
                <!-- Customer Information -->
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fas fa-user text-primary-600 mr-2"></i>
                        Informasi Pelanggan
                    </h2>
                    
                    @auth
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="shipping_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input type="text" id="shipping_name" name="shipping_name" value="{{ auth()->user()->name }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div>
                                <label for="shipping_email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" id="shipping_email" name="shipping_email" value="{{ auth()->user()->email }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div class="md:col-span-2">
                                <label for="shipping_phone" class="block text-sm font-medium text-gray-700">No. Telepon</label>
                                <input type="tel" id="shipping_phone" name="shipping_phone" value="{{ auth()->user()->phone ?? '' }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="08xxxxxxxxxx">
                            </div>
                        </div>
                    @else
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-4">
                            <div class="flex">
                                <i class="fas fa-info-circle text-blue-400 mr-2 mt-0.5"></i>
                                <div>
                                    <h3 class="text-sm font-medium text-blue-800">Login untuk Checkout Lebih Mudah</h3>
                                    <p class="mt-1 text-sm text-blue-700">
                                        <a href="{{ route('login') }}" class="font-medium underline">Login</a> 
                                        atau 
                                        <a href="{{ route('register') }}" class="font-medium underline">daftar</a> 
                                        untuk menyimpan informasi dan melacak pesanan.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="shipping_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input type="text" id="shipping_name" name="shipping_name" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div>
                                <label for="shipping_email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" id="shipping_email" name="shipping_email" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div class="md:col-span-2">
                                <label for="shipping_phone" class="block text-sm font-medium text-gray-700">No. Telepon</label>
                                <input type="tel" id="shipping_phone" name="shipping_phone" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="08xxxxxxxxxx">
                            </div>
                        </div>
                    @endauth
                </div>

                <!-- Shipping Address -->
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fas fa-map-marker-alt text-primary-600 mr-2"></i>
                        Alamat Pengiriman
                    </h2>
                    
                    @auth
                    @if($addresses->count() > 0)
                    <!-- Saved Addresses -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-3">
                            <label class="block text-sm font-medium text-gray-700">Pilih Alamat Tersimpan</label>
                            <a href="{{ route('profile.show') }}" target="_blank" class="text-sm text-primary-600 hover:text-primary-700">
                                <i class="fas fa-plus mr-1"></i>Tambah Alamat Baru
                            </a>
                        </div>
                        <div class="space-y-3">
                            @foreach($addresses as $address)
                            <div class="saved-address-option border border-gray-200 rounded-lg p-4 cursor-pointer hover:border-primary-500 hover:bg-primary-50 transition-colors"
                                 data-address='@json($address)'
                                 onclick="selectSavedAddress(this)">
                                <div class="flex items-start">
                                    <input type="radio" name="saved_address" value="{{ $address->id }}" class="mt-1 text-primary-600 focus:ring-primary-500">
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-semibold text-gray-900">{{ $address->label }}</span>
                                            @if($address->is_default)
                                            <span class="px-2 py-0.5 bg-primary-100 text-primary-700 text-xs rounded-full">Utama</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-900 mt-1">{{ $address->recipient_name }} | {{ $address->phone }}</p>
                                        <p class="text-sm text-gray-600 mt-1">{{ $address->full_address }}</p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4">
                            <label class="flex items-center text-sm">
                                <input type="checkbox" id="use-different-address" class="text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <span class="ml-2 text-gray-700">Gunakan alamat lain</span>
                            </label>
                        </div>
                    </div>
                    @endif
                    @endauth
                    
                    <!-- Manual Address Form -->
                    <div id="manual-address-form" class="{{ auth()->check() && $addresses->count() > 0 ? 'hidden' : '' }}">
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label for="shipping_address" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                                <textarea id="shipping_address" name="shipping_address" rows="3" {{ auth()->check() && $addresses->count() > 0 ? '' : 'required' }}
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                          placeholder="Jalan, nomor rumah, RT/RW, kelurahan..."></textarea>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="shipping_province" class="block text-sm font-medium text-gray-700">Provinsi</label>
                                    <select id="shipping_province" name="shipping_province_id" {{ auth()->check() && $addresses->count() > 0 ? '' : 'required' }}
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Pilih Provinsi</option>
                                    </select>
                                    <input type="hidden" id="shipping_state" name="shipping_state">
                                </div>
                                <div>
                                    <label for="shipping_city" class="block text-sm font-medium text-gray-700">Kota/Kabupaten</label>
                                    <select id="shipping_city" name="shipping_city_id" {{ auth()->check() && $addresses->count() > 0 ? '' : 'required' }} disabled
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 disabled:bg-gray-100">
                                        <option value="">Pilih Provinsi Dulu</option>
                                    </select>
                                    <input type="hidden" id="shipping_city_name" name="shipping_city">
                                </div>
                            </div>
                            
                            <div>
                                <label for="shipping_postal_code" class="block text-sm font-medium text-gray-700">Kode Pos</label>
                                <input type="text" id="shipping_postal_code" name="shipping_postal_code" {{ auth()->check() && $addresses->count() > 0 ? '' : 'required' }}
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="Masukkan kode pos">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Method -->
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fas fa-truck text-primary-600 mr-2"></i>
                        Metode Pengiriman
                    </h2>
                    
                    <div id="shipping-methods-container">
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-map-marked-alt text-4xl mb-3 opacity-50"></i>
                            <p>Pilih provinsi dan kota tujuan untuk melihat opsi pengiriman</p>
                        </div>
                    </div>

                    <!-- Hidden fields for shipping data -->
                    <input type="hidden" id="shipping_courier" name="shipping_courier">
                    <input type="hidden" id="shipping_service" name="shipping_service">
                    <input type="hidden" id="shipping_etd" name="shipping_etd">
                    <input type="hidden" id="shipping_cost" name="shipping_amount">
                    <input type="hidden" id="shipping_weight" name="shipping_weight" value="{{ $totalWeight ?? 0 }}">
                </div>

                <!-- Payment Info -->
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fas fa-credit-card text-primary-600 mr-2"></i>
                        Metode Pembayaran
                    </h2>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-3"></i>
                            <div>
                                <h3 class="text-sm font-semibold text-blue-900 mb-2">Pembayaran via Xendit</h3>
                                <p class="text-sm text-blue-800 mb-3">
                                    Setelah konfirmasi pesanan, Anda akan diarahkan ke halaman pembayaran Xendit untuk memilih metode pembayaran.
                                </p>
                                <div class="text-sm text-blue-700">
                                    <p class="font-medium mb-1">Metode pembayaran yang tersedia:</p>
                                    <ul class="list-disc list-inside space-y-1 ml-2">
                                        <li>Transfer Bank (BCA, Mandiri, BNI, BRI, Permata, dll)</li>
                                        <li>E-Wallet (GoPay, OVO, DANA, LinkAja, ShopeePay)</li>
                                        <li>QRIS (Scan untuk bayar)</li>
                                        <li>Virtual Account</li>
                                        <li>Kartu Kredit/Debit</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden field for payment method (will be set by Xendit) -->
                    <input type="hidden" name="payment_method" value="xendit">
                </div>

                <!-- Order Notes -->
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fas fa-sticky-note text-primary-600 mr-2"></i>
                        Catatan Pesanan (Opsional)
                    </h2>
                    
                    <textarea id="notes" name="notes" rows="3"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"
                              placeholder="Tambahkan catatan khusus untuk pesanan Anda..."></textarea>
                </div>
            </form>
        </div>

        <!-- Right Column - Order Summary -->
        <div class="lg:col-span-5 mt-10 lg:mt-0">
            <div class="bg-white shadow rounded-lg sticky top-4">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-receipt text-primary-600 mr-2"></i>
                        Ringkasan Pesanan
                    </h2>
                </div>
                
                <div class="px-6 py-4">
                    <!-- Cart Items -->
                    <div class="space-y-4 mb-6" id="checkout-items">
                        @forelse($cartItems as $cartItem)
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 w-16 h-16">
                                    @if($cartItem->product->first_image)
                                        <img class="w-16 h-16 rounded-md object-cover" 
                                             src="{{ $cartItem->product->first_image }}" 
                                             alt="{{ $cartItem->product->name }}"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="w-16 h-16 bg-gray-100 rounded-md items-center justify-center hidden">
                                            <i class="fas fa-box-open text-gray-400"></i>
                                        </div>
                                    @else
                                        <div class="w-16 h-16 bg-gray-100 rounded-md flex items-center justify-center">
                                            <i class="fas fa-box-open text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 truncate">{{ $cartItem->product->name }}</h4>
                                    <p class="text-sm text-gray-500">Qty: {{ $cartItem->quantity }}</p>
                                </div>
                                <div class="text-sm font-medium text-gray-900">
                                    Rp {{ number_format($cartItem->total, 0, ',', '.') }}
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500">Keranjang kosong</p>
                        @endforelse
                    </div>

                    @if($cartItems->count() > 0)
                        <!-- Discount Code -->
                        <div class="border-t border-gray-200 pt-4 mb-6">
                            @if(session('discount_code'))
                                <!-- Discount Applied -->
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-tag text-green-600"></i>
                                            <div>
                                                <p class="text-sm font-medium text-green-900">Kode diskon diterapkan</p>
                                                <p class="text-xs text-green-700">{{ session('discount_code') }} ({{ session('discount_percentage') }}% off)</p>
                                            </div>
                                        </div>
                                        <button type="button" id="remove-discount" 
                                                class="px-3 py-1.5 bg-red-100 text-red-700 hover:bg-red-200 hover:text-red-800 rounded-md text-sm font-medium transition-colors duration-200 flex items-center">
                                            <i class="fas fa-times mr-1"></i>
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            @else
                                <!-- Discount Input -->
                                <div class="space-y-2">
                                    <div class="flex space-x-2">
                                        <div class="relative flex-1">
                                            <input type="text" id="discount_code" placeholder="Masukkan kode diskon" 
                                                   value="{{ old('discount_code') }}"
                                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 text-sm pr-8">
                                            <button type="button" id="clear-discount-input"
                                                    class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden"
                                                    title="Hapus teks">
                                                <i class="fas fa-times-circle"></i>
                                            </button>
                                        </div>
                                        <button type="button" id="apply-discount"
                                                class="bg-primary-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-primary-700 transition whitespace-nowrap">
                                            Terapkan
                                        </button>
                                    </div>
                                    <!-- Discount hints -->
                                    <p class="text-xs text-gray-500">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Contoh kode: WELCOME10, SAVE15, NEWUSER20
                                    </p>
                                </div>
                            @endif
                        </div>

                        <!-- Order Summary -->
                        <div class="border-t border-gray-200 pt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="text-gray-900" id="subtotal-amount">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Ongkos Kirim</span>
                                <span class="text-gray-900" id="shipping-amount">Rp {{ number_format($shippingAmount, 0, ',', '.') }}</span>
                            </div>
                            @if($discountAmount > 0)
                            <div class="flex justify-between text-sm" id="discount-row">
                                <span class="text-gray-600">Diskon</span>
                                <span class="text-green-600" id="discount-amount">-Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
                            </div>
                            @else
                            <div class="flex justify-between text-sm" id="discount-row" style="display: none;">
                                <span class="text-gray-600">Diskon</span>
                                <span class="text-green-600" id="discount-amount">-Rp 0</span>
                            </div>
                            @endif
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">PPN (11%)</span>
                                <span class="text-gray-900" id="tax-amount">Rp {{ number_format($taxAmount, 0, ',', '.') }}</span>
                            </div>
                            <div class="border-t border-gray-200 pt-2">
                                <div class="flex justify-between text-base font-medium">
                                    <span class="text-gray-900">Total</span>
                                    <span class="text-gray-900" id="total-amount">
                                        Rp {{ number_format($total, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Checkout Button -->
                        <div class="mt-6">
                            <button type="submit" form="checkout-form"
                                    class="w-full bg-primary-600 border border-transparent rounded-md shadow-sm py-3 px-4 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <i class="fas fa-lock mr-2"></i>
                                Konfirmasi Pesanan
                            </button>
                            
                            <p class="mt-2 text-xs text-center text-gray-500">
                                <i class="fas fa-shield-alt mr-1"></i>
                                Pembayaran aman dan data terlindungi
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const provinceSelect = document.getElementById('shipping_province');
    const citySelect = document.getElementById('shipping_city');
    const shippingMethodsContainer = document.getElementById('shipping-methods-container');
    const shippingAmountEl = document.getElementById('shipping-amount');
    const weight = parseInt(document.getElementById('shipping_weight').value) || 1000;
    
    let selectedShipping = null;

    // Handle saved address selection
    const useDifferentAddressCheckbox = document.getElementById('use-different-address');
    const manualAddressForm = document.getElementById('manual-address-form');
    
    // Auto-select default address if available
    const defaultAddressOption = document.querySelector('.saved-address-option input[type="radio"]');
    if (defaultAddressOption && !useDifferentAddressCheckbox) {
        // Auto-select first address (usually the default one)
        setTimeout(() => {
            defaultAddressOption.closest('.saved-address-option').click();
        }, 500);
    }
    
    if (useDifferentAddressCheckbox) {
        useDifferentAddressCheckbox.addEventListener('change', function() {
            if (this.checked) {
                manualAddressForm.classList.remove('hidden');
                // Enable required attributes
                manualAddressForm.querySelectorAll('input, select, textarea').forEach(field => {
                    if (field.id !== 'shipping_state' && field.id !== 'shipping_city_name') {
                        field.required = true;
                    }
                });
                // Clear selected saved address
                document.querySelectorAll('.saved-address-option').forEach(opt => {
                    opt.classList.remove('border-primary-500', 'bg-primary-50');
                    opt.classList.add('border-gray-200');
                    opt.querySelector('input[type="radio"]').checked = false;
                });
            } else {
                manualAddressForm.classList.add('hidden');
                // Disable required attributes
                manualAddressForm.querySelectorAll('input, select, textarea').forEach(field => {
                    field.required = false;
                });
            }
        });
    }

    // Load provinces on page load
    loadProvinces();

    async function loadProvinces() {
        try {
            const response = await fetch('/shipping/provinces');
            const result = await response.json();
            
            if (result.success && result.data) {
                provinceSelect.innerHTML = '<option value="">Pilih Provinsi</option>';
                result.data.forEach(province => {
                    const option = new Option(province.province, province.province_id);
                    option.dataset.provinceName = province.province;
                    provinceSelect.add(option);
                });
            }
        } catch (error) {
            console.error('Error loading provinces:', error);
            showNotification('Gagal memuat data provinsi', 'error');
        }
    }

    provinceSelect.addEventListener('change', async function() {
        const provinceId = this.value;
        const provinceName = this.options[this.selectedIndex]?.dataset.provinceName || '';
        
        // Set hidden field
        document.getElementById('shipping_state').value = provinceName;
        
        // Reset city and shipping options
        citySelect.innerHTML = '<option value="">Memuat...</option>';
        citySelect.disabled = true;
        shippingMethodsContainer.innerHTML = '<div class="text-center py-4 text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat...</div>';
        
        if (!provinceId) {
            citySelect.innerHTML = '<option value="">Pilih Provinsi Dulu</option>';
            shippingMethodsContainer.innerHTML = '<div class="text-center py-8 text-gray-500"><i class="fas fa-map-marked-alt text-4xl mb-3 opacity-50"></i><p>Pilih provinsi dan kota tujuan untuk melihat opsi pengiriman</p></div>';
            return;
        }

        try {
            const response = await fetch(`/shipping/cities?province_id=${provinceId}`);
            const result = await response.json();
            
            if (result.success && result.data) {
                citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                result.data.forEach(city => {
                    const cityName = `${city.type} ${city.city_name}`;
                    const option = new Option(cityName, city.city_id);
                    option.dataset.cityName = cityName;
                    option.dataset.postalCode = city.postal_code;
                    citySelect.add(option);
                });
                citySelect.disabled = false;
            }
        } catch (error) {
            console.error('Error loading cities:', error);
            showNotification('Gagal memuat data kota', 'error');
            citySelect.innerHTML = '<option value="">Error memuat data</option>';
        }
    });

    citySelect.addEventListener('change', async function() {
        const cityId = this.value;
        const cityName = this.options[this.selectedIndex]?.dataset.cityName || '';
        const postalCode = this.options[this.selectedIndex]?.dataset.postalCode || '';
        
        // Set hidden fields
        document.getElementById('shipping_city_name').value = cityName;
        if (postalCode && !document.getElementById('shipping_postal_code').value) {
            document.getElementById('shipping_postal_code').value = postalCode;
        }
        
        if (!cityId) {
            shippingMethodsContainer.innerHTML = '<div class="text-center py-8 text-gray-500"><i class="fas fa-map-marked-alt text-4xl mb-3 opacity-50"></i><p>Pilih provinsi dan kota tujuan untuk melihat opsi pengiriman</p></div>';
            return;
        }

        // Load shipping costs
        shippingMethodsContainer.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-2xl text-primary-600"></i><p class="mt-2 text-sm text-gray-600">Menghitung ongkos kirim...</p></div>';

        try {
            const response = await fetch('/shipping/costs', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    destination_city_id: cityId,
                    weight: weight
                })
            });

            const result = await response.json();
            
            if (result.success && result.data) {
                displayShippingOptions(result.data);
            } else {
                shippingMethodsContainer.innerHTML = '<div class="text-center py-4 text-red-600"><i class="fas fa-exclamation-circle mr-2"></i>Gagal menghitung ongkos kirim</div>';
            }
        } catch (error) {
            console.error('Error calculating shipping:', error);
            shippingMethodsContainer.innerHTML = '<div class="text-center py-4 text-red-600"><i class="fas fa-exclamation-circle mr-2"></i>Terjadi kesalahan</div>';
        }
    });

    function displayShippingOptions(couriers) {
        let html = '<div class="space-y-3">';
        
        couriers.forEach(courier => {
            if (courier.costs && courier.costs.length > 0) {
                courier.costs.forEach(cost => {
                    if (cost.cost && cost.cost.length > 0) {
                        const service = cost.cost[0];
                        const id = `shipping_${courier.code}_${cost.service}`.toLowerCase().replace(/[^a-z0-9_]/g, '_');
                        
                        html += `
                            <div class="relative border border-gray-200 rounded-lg p-4 hover:border-primary-500 hover:bg-primary-50 transition cursor-pointer shipping-option" data-id="${id}">
                                <input id="${id}" type="radio" name="shipping_method" value="${id}" 
                                       data-courier="${courier.code}" 
                                       data-service="${cost.service}" 
                                       data-cost="${service.value}"
                                       data-etd="${service.etd}"
                                       class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300">
                                <label for="${id}" class="ml-3 block cursor-pointer">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <span class="font-medium text-gray-900 uppercase">${courier.code}</span>
                                            <span class="text-gray-600">- ${cost.service}</span>
                                            <p class="text-sm text-gray-500 mt-1">${cost.description || ''}</p>
                                            <p class="text-sm text-gray-600 mt-1">
                                                <i class="fas fa-clock text-xs mr-1"></i>
                                                Estimasi ${service.etd} hari
                                            </p>
                                        </div>
                                        <span class="font-bold text-primary-600 whitespace-nowrap ml-4">
                                            Rp ${service.value.toLocaleString('id-ID')}
                                        </span>
                                    </div>
                                </label>
                            </div>
                        `;
                    }
                });
            }
        });
        
        html += '</div>';
        
        if (html === '<div class="space-y-3"></div>') {
            html = '<div class="text-center py-4 text-gray-500">Tidak ada opsi pengiriman tersedia</div>';
        }
        
        shippingMethodsContainer.innerHTML = html;

        // Add click event to shipping options
        document.querySelectorAll('.shipping-option').forEach(option => {
            option.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
                updateShippingSelection(radio);
            });
        });

        // Add change event to radio buttons
        document.querySelectorAll('input[name="shipping_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                updateShippingSelection(this);
            });
        });
    }

    function updateShippingSelection(radio) {
        if (radio.checked) {
            const courier = radio.dataset.courier;
            const service = radio.dataset.service;
            const cost = parseInt(radio.dataset.cost);
            const etd = radio.dataset.etd;

            // Update hidden fields
            document.getElementById('shipping_courier').value = courier;
            document.getElementById('shipping_service').value = service;
            document.getElementById('shipping_cost').value = cost;
            document.getElementById('shipping_etd').value = etd;

            // Update summary
            shippingAmountEl.textContent = 'Rp ' + cost.toLocaleString('id-ID');
            
            // Update visual selection
            document.querySelectorAll('.shipping-option').forEach(opt => {
                opt.classList.remove('border-primary-500', 'bg-primary-50');
                opt.classList.add('border-gray-200');
            });
            radio.closest('.shipping-option').classList.remove('border-gray-200');
            radio.closest('.shipping-option').classList.add('border-primary-500', 'bg-primary-50');

            updateTotal();
        }
    }
    
        // Clear discount input field
        const discountInput = document.getElementById('discount_code');
        const clearInputBtn = document.getElementById('clear-discount-input');
        
        if (discountInput && clearInputBtn) {
            // Show/hide clear button based on input value
            discountInput.addEventListener('input', function() {
                if (this.value.trim()) {
                    clearInputBtn.classList.remove('hidden');
                } else {
                    clearInputBtn.classList.add('hidden');
                }
            });
            
            // Clear input when button clicked
            clearInputBtn.addEventListener('click', function() {
                discountInput.value = '';
                this.classList.add('hidden');
                discountInput.focus();
            });
            
            // Initial check
            if (discountInput.value.trim()) {
                clearInputBtn.classList.remove('hidden');
            }
        }
        
        // Apply discount code
        document.getElementById('apply-discount')?.addEventListener('click', async function() {
            const discountCode = document.getElementById('discount_code').value.trim();
            
            if (!discountCode) {
                showNotification('Masukkan kode diskon terlebih dahulu', 'error');
                return;
            }

            // Show loading
            const originalHTML = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Memproses...';
            
            try {
                const response = await fetch('/cart/discount', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        discount_code: discountCode
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    showNotification(result.message || 'Kode diskon berhasil diterapkan!', 'success');
                    
                    // Reload page to show the discount applied state
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showNotification(result.message || 'Kode diskon tidak valid', 'error');
                    this.disabled = false;
                    this.innerHTML = originalHTML;
                }
            } catch (error) {
                console.error('Error applying discount:', error);
                showNotification('Terjadi kesalahan saat menerapkan kode diskon', 'error');
                this.disabled = false;
                this.innerHTML = originalHTML;
            }
        });

    // Remove/Clear discount code
    document.getElementById('remove-discount')?.addEventListener('click', async function() {
        // Show loading state
        const originalHTML = this.innerHTML;
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Menghapus...';
        
        try {
            const response = await fetch('/cart/discount', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const result = await response.json();
            
            if (result.success) {
                showNotification(result.message || 'Kode diskon berhasil dihapus', 'success');
                
                // Reload page to show the cleared state
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showNotification(result.message || 'Gagal menghapus kode diskon', 'error');
                this.disabled = false;
                this.innerHTML = originalHTML;
            }
        } catch (error) {
            console.error('Error removing discount:', error);
            showNotification('Terjadi kesalahan saat menghapus kode diskon', 'error');
            this.disabled = false;
            this.innerHTML = originalHTML;
        }
    });
    
    function updateTotal() {
        const subtotal = parseInt(document.getElementById('subtotal-amount').textContent.replace(/[^\d]/g, ''));
        const shipping = parseInt(document.getElementById('shipping-amount').textContent.replace(/[^\d]/g, '')) || 0;
        const tax = subtotal * 0.11;
        
        let discount = 0;
        const discountEl = document.getElementById('discount-amount');
        if (discountEl && document.getElementById('discount-row').style.display !== 'none') {
            discount = parseInt(discountEl.textContent.replace(/[^\d]/g, ''));
        }
        
        const total = subtotal + shipping + tax - discount;
        
        document.getElementById('tax-amount').textContent = 'Rp ' + tax.toLocaleString('id-ID');
        document.getElementById('total-amount').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    function showNotification(message, type) {
        // Simple notification - you can enhance this
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            info: 'bg-blue-500'
        };
        
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});

// Function to select saved address
function selectSavedAddress(element) {
    const radio = element.querySelector('input[type="radio"]');
    radio.checked = true;
    
    // Update visual selection
    document.querySelectorAll('.saved-address-option').forEach(opt => {
        opt.classList.remove('border-primary-500', 'bg-primary-50');
        opt.classList.add('border-gray-200');
    });
    element.classList.remove('border-gray-200');
    element.classList.add('border-primary-500', 'bg-primary-50');
    
    // Get address data
    const addressData = JSON.parse(element.dataset.address);
    
    // Fill in the form fields
    document.getElementById('shipping_address').value = addressData.address;
    document.getElementById('shipping_postal_code').value = addressData.postal_code;
    
    // Update customer info if available
    const shippingName = document.getElementById('shipping_name');
    const shippingPhone = document.getElementById('shipping_phone');
    if (shippingName && !shippingName.value) {
        shippingName.value = addressData.recipient_name;
    }
    if (shippingPhone && !shippingPhone.value) {
        shippingPhone.value = addressData.phone;
    }
    
    // Set province and city (we'll need to load them)
    loadProvinceAndCity(addressData.province, addressData.city);
    
    // Uncheck the "use different address" checkbox
    const useDifferentAddress = document.getElementById('use-different-address');
    if (useDifferentAddress) {
        useDifferentAddress.checked = false;
        document.getElementById('manual-address-form').classList.add('hidden');
    }
}

async function loadProvinceAndCity(provinceName, cityName) {
    try {
        // Load provinces first
        const provinceSelect = document.getElementById('shipping_province');
        const citySelect = document.getElementById('shipping_city');
        
        const response = await fetch('/shipping/provinces');
        const result = await response.json();
        
        if (result.success && result.data) {
            // Find matching province
            const province = result.data.find(p => p.province.toLowerCase() === provinceName.toLowerCase());
            
            if (province) {
                provinceSelect.value = province.province_id;
                document.getElementById('shipping_state').value = province.province;
                
                // Trigger change to load cities
                const event = new Event('change');
                provinceSelect.dispatchEvent(event);
                
                // Wait a bit for cities to load, then select the city
                setTimeout(async () => {
                    const citiesResponse = await fetch(`/shipping/cities?province_id=${province.province_id}`);
                    const citiesResult = await citiesResponse.json();
                    
                    if (citiesResult.success && citiesResult.data) {
                        const city = citiesResult.data.find(c => {
                            const fullCityName = `${c.type} ${c.city_name}`.toLowerCase();
                            return fullCityName === cityName.toLowerCase() || c.city_name.toLowerCase() === cityName.toLowerCase();
                        });
                        
                        if (city) {
                            citySelect.value = city.city_id;
                            document.getElementById('shipping_city_name').value = `${city.type} ${city.city_name}`;
                            
                            // Trigger change to load shipping options
                            const cityEvent = new Event('change');
                            citySelect.dispatchEvent(cityEvent);
                        }
                    }
                }, 500);
            }
        }
    } catch (error) {
        console.error('Error loading province and city:', error);
    }
}
</script>
@endsection