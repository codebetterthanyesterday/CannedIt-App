<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'CannedIt - Toko Online Makanan Kaleng Terpercaya')</title>
    <meta name="description" content="@yield('description', 'Jual makanan kaleng berkualitas dengan harga terjangkau. Tersedia berbagai macam produk sayuran, buah, daging, dan seafood kaleng.')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fef7ee',
                            100: '#fdedd3',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                        }
                    }
                }
            }
        }
    </script>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-lg sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="flex items-center space-x-2">
                            <i class="fas fa-shopping-cart text-primary-600 text-2xl"></i>
                            <span class="font-bold text-xl text-gray-800">CannedIt</span>
                        </a>
                    </div>

                    <!-- Search Bar -->
                    <div class="hidden md:flex flex-1 max-w-lg mx-8">
                        <div class="w-full relative">
                            <div class="relative">
                                <input type="text" 
                                       id="search-input"
                                       placeholder="Cari produk makanan kaleng..." 
                                       autocomplete="off"
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <div id="search-loading" class="hidden">
                                        <i class="fas fa-spinner fa-spin text-primary-600"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Search Results Dropdown -->
                            <div id="search-results" class="hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-lg shadow-lg border border-gray-200 max-h-96 overflow-y-auto z-50">
                                <div id="search-results-content"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Menu -->
                    <div class="flex items-center space-x-4">
                        <!-- Categories Dropdown -->
                        <div class="relative group">
                            <button class="flex items-center space-x-1 text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md">
                                <i class="fas fa-th-large"></i>
                                <span class="hidden sm:block">Kategori</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <div class="absolute top-full left-0 mt-1 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <div class="py-1">
                                    <a href="{{ route('products.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-th-large mr-2"></i>Semua Produk
                                    </a>
                                    @foreach($globalCategories as $category)
                                        <a href="{{ route('products.index', ['category' => $category->id]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            @if($category->icon)
                                                <i class="{{ $category->icon }} mr-2 {{ $category->color ?? 'text-gray-400' }}"></i>
                                            @else
                                                <i class="fas fa-box mr-2 text-gray-400"></i>
                                            @endif
                                            {{ $category->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Cart (Customer only) -->
                        @if(!Auth::check() || !Auth::user()->is_admin)
                        <a href="{{ route('cart.index') }}" class="relative text-gray-700 hover:text-primary-600 p-2">
                            <i class="fas fa-shopping-cart text-xl"></i>
                            <span id="cart-count" class="hidden absolute -top-1 -right-1 bg-primary-600 text-white text-xs rounded-full h-5 w-5 items-center justify-center">0</span>
                        </a>
                        @endif

                        @auth
                        <!-- Wishlist (Customer only) -->
                        @if(!Auth::user()->is_admin)
                        <a href="{{ route('wishlist.index') }}" class="relative text-gray-700 hover:text-primary-600 p-2">
                            <i class="fas fa-heart text-xl"></i>
                            <span id="wishlist-count" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 items-center justify-center">0</span>
                        </a>
                        @endif
                        @endauth

                        <!-- User Menu -->
                        @auth
                            <div class="relative group">
                                <button class="flex items-center space-x-2 text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md">
                                    @php
                                        $hasAvatar = Auth::user()->avatar && 
                                                    !str_contains(Auth::user()->avatar, 'lh3.googleusercontent.com/a/default') &&
                                                    (str_contains(Auth::user()->avatar, 'googleusercontent.com') || 
                                                     file_exists(public_path(Auth::user()->avatar)));
                                    @endphp
                                    
                                    @if($hasAvatar)
                                        <img src="{{ str_contains(Auth::user()->avatar, 'http') ? Auth::user()->avatar : asset(Auth::user()->avatar) }}" 
                                             alt="Avatar" 
                                             class="w-8 h-8 rounded-full object-cover"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 items-center justify-center hidden">
                                            <span class="text-xs font-bold text-white">
                                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', Auth::user()->name)[1] ?? Auth::user()->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
                                            <span class="text-xs font-bold text-white">
                                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', Auth::user()->name)[1] ?? Auth::user()->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                    <span class="hidden sm:block">{{ Auth::user()->name }}</span>
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </button>
                                <div class="absolute top-full right-0 mt-1 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                    <div class="py-1">
                                        @if(Auth::user()->is_admin)
                                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-shield-alt mr-2 text-primary-600"></i>Admin Panel
                                            </a>
                                            <div class="border-t border-gray-200"></div>
                                        @endif
                                        <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-user mr-2"></i>Profil Saya
                                        </a>
                                        @if(!Auth::user()->is_admin)
                                        <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-box mr-2"></i>Pesanan Saya
                                        </a>
                                        @endif
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('login') }}" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md">
                                    <i class="fas fa-sign-in-alt mr-1"></i>
                                    <span class="hidden sm:inline">Login</span>
                                </a>
                                <a href="{{ route('register') }}" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 transition-colors">
                                    <i class="fas fa-user-plus mr-1"></i>
                                    <span class="hidden sm:inline">Daftar</span>
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>

                <!-- Mobile Search -->
                <div class="md:hidden pb-4">
                    <form action="{{ route('products.index') }}" method="GET">
                        <div class="relative">
                            <input type="text" 
                                   name="search" 
                                   placeholder="Cari produk..." 
                                   value="{{ request('search') }}"
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: '{{ session('success') }}',
                        showConfirmButton: false,
                        timer: 3000,
                        toast: true,
                        position: 'top-end'
                    });
                });
            </script>
        @endif

        @if(session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: '{{ session('error') }}',
                        showConfirmButton: true,
                        confirmButtonColor: '#ea580c'
                    });
                });
            </script>
        @endif

        @if(session('warning'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian!',
                        text: '{{ session('warning') }}',
                        showConfirmButton: true,
                        confirmButtonColor: '#ea580c'
                    });
                });
            </script>
        @endif

        @if(session('info'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'info',
                        title: 'Informasi',
                        text: '{{ session('info') }}',
                        showConfirmButton: false,
                        timer: 3000,
                        toast: true,
                        position: 'top-end'
                    });
                });
            </script>
        @endif

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="text-center text-sm text-gray-400">
                    <p>&copy; {{ date('Y') }} CannedIt. Semua hak dilindungi undang-undang.</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script>
        // Load cart count on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
            updateWishlistCount();
            initLiveSearch();
        });

        function updateCartCount() {
            const cartCountElement = document.getElementById('cart-count');
            if (!cartCountElement) return;
            
            fetch('{{ route("cart.count") }}')
                .then(response => response.json())
                .then(data => {
                    const count = data.count;
                    cartCountElement.textContent = count;
                    
                    // Show/hide badge based on count
                    if (count > 0) {
                        cartCountElement.classList.remove('hidden');
                        cartCountElement.classList.add('flex');
                    } else {
                        cartCountElement.classList.add('hidden');
                        cartCountElement.classList.remove('flex');
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function updateWishlistCount() {
            const wishlistCount = document.getElementById('wishlist-count');
            if (!wishlistCount) return;
            
            fetch('{{ route("wishlist.count") }}')
                .then(response => response.json())
                .then(data => {
                    const count = data.count;
                    wishlistCount.textContent = count;
                    
                    // Show/hide badge based on count
                    if (count > 0) {
                        wishlistCount.classList.remove('hidden');
                        wishlistCount.classList.add('flex');
                    } else {
                        wishlistCount.classList.add('hidden');
                        wishlistCount.classList.remove('flex');
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Live Search Implementation
        function initLiveSearch() {
            const searchInput = document.getElementById('search-input');
            const searchResults = document.getElementById('search-results');
            const searchResultsContent = document.getElementById('search-results-content');
            const searchLoading = document.getElementById('search-loading');
            let searchTimeout;

            if (!searchInput) return;

            searchInput.addEventListener('input', function(e) {
                const query = e.target.value.trim();
                
                // Clear previous timeout
                clearTimeout(searchTimeout);
                
                // Hide results if query is empty
                if (query.length === 0) {
                    searchResults.classList.add('hidden');
                    return;
                }
                
                // Show loading
                searchLoading.classList.remove('hidden');
                
                // Debounce search
                searchTimeout = setTimeout(() => {
                    performSearch(query);
                }, 300);
            });

            // Close results when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.classList.add('hidden');
                }
            });

            // Handle keyboard navigation
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    searchResults.classList.add('hidden');
                    searchInput.blur();
                }
            });
        }

        function performSearch(query) {
            const searchResults = document.getElementById('search-results');
            const searchResultsContent = document.getElementById('search-results-content');
            const searchLoading = document.getElementById('search-loading');

            fetch(`{{ route('products.search') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    searchLoading.classList.add('hidden');
                    
                    if (data.length === 0) {
                        searchResultsContent.innerHTML = `
                            <div class="p-4 text-center text-gray-500">
                                <i class="fas fa-search text-2xl mb-2"></i>
                                <p class="text-sm">Tidak ada produk ditemukan untuk "${query}"</p>
                            </div>
                        `;
                        searchResults.classList.remove('hidden');
                        return;
                    }
                    
                    let html = '<div class="py-2">';
                    data.forEach(product => {
                        const imageUrl = product.image || '{{ asset("images/no-image.svg") }}';
                        html += `
                            <a href="${product.url}" class="flex items-center space-x-3 px-4 py-3 hover:bg-gray-50 transition-colors">
                                <img src="${imageUrl}" 
                                     alt="${product.name}" 
                                     class="w-12 h-12 rounded object-cover"
                                     onerror="this.src='{{ asset("images/no-image.svg") }}'; this.onerror=null;">
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 truncate">${product.name}</h4>
                                    <p class="text-xs text-gray-500">${product.category}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-primary-600">${product.price_formatted}</p>
                                </div>
                            </a>
                        `;
                    });
                    html += '</div>';
                    
                    html += `
                        <div class="border-t border-gray-200 px-4 py-3 bg-gray-50">
                            <a href="{{ route('products.index') }}?search=${encodeURIComponent(query)}" 
                               class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                                <i class="fas fa-arrow-right mr-1"></i>
                                Lihat semua hasil untuk "${query}"
                            </a>
                        </div>
                    `;
                    
                    searchResultsContent.innerHTML = html;
                    searchResults.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Search error:', error);
                    searchLoading.classList.add('hidden');
                    searchResultsContent.innerHTML = `
                        <div class="p-4 text-center text-red-500">
                            <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                            <p class="text-sm">Terjadi kesalahan saat mencari produk</p>
                        </div>
                    `;
                    searchResults.classList.remove('hidden');
                });
        }

        // Helper function to show SweetAlert notifications
        function showNotification(type, message) {
            const config = {
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            };

            switch(type) {
                case 'success':
                    Swal.fire({
                        ...config,
                        icon: 'success',
                        title: message
                    });
                    break;
                case 'error':
                    Swal.fire({
                        ...config,
                        icon: 'error',
                        title: message
                    });
                    break;
                case 'warning':
                    Swal.fire({
                        ...config,
                        icon: 'warning',
                        title: message
                    });
                    break;
                case 'info':
                    Swal.fire({
                        ...config,
                        icon: 'info',
                        title: message
                    });
                    break;
            }
        }

        // Helper function to show confirmation dialog
        function showConfirmation(title, text, confirmButtonText = 'Ya, Lanjutkan!', cancelButtonText = 'Batal') {
            return Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea580c',
                cancelButtonColor: '#6b7280',
                confirmButtonText: confirmButtonText,
                cancelButtonText: cancelButtonText,
                reverseButtons: true
            });
        }

        // Auto-hide flash messages (legacy fallback)
        setTimeout(function() {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    </script>

    @stack('scripts')
</body>
</html>