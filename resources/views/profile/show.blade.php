@extends('layouts.app')

@section('title', 'Profil Saya - CannedIt')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Profil Saya</h1>
        <p class="text-gray-600 mt-1">Kelola informasi profil dan alamat pengiriman Anda</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <!-- Avatar -->
                <div class="flex flex-col items-center">
                    <div class="relative">
                        @php
                            $hasAvatar = $user->avatar && 
                                        !str_contains($user->avatar, 'lh3.googleusercontent.com/a/default') &&
                                        (str_contains($user->avatar, 'googleusercontent.com') || 
                                         file_exists(public_path($user->avatar)));
                        @endphp
                        
                        @if($hasAvatar)
                            <img src="{{ str_contains($user->avatar, 'http') ? $user->avatar : asset($user->avatar) }}" 
                                 alt="Avatar" 
                                 class="w-32 h-32 rounded-full object-cover border-4 border-gray-200"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-32 h-32 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 items-center justify-center border-4 border-gray-200 hidden">
                                <span class="text-4xl font-bold text-white">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $user->name)[1] ?? $user->name, 0, 1)) }}
                                </span>
                            </div>
                        @else
                            <div class="w-32 h-32 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center border-4 border-gray-200">
                                <span class="text-4xl font-bold text-white">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $user->name)[1] ?? $user->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <button onclick="document.getElementById('avatar-input').click()" class="absolute bottom-0 right-0 bg-primary-600 text-white rounded-full p-2 hover:bg-primary-700 transition-colors">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                    
                    <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" id="avatar-form" class="hidden">
                        @csrf
                        @method('PUT')
                        <input type="file" name="avatar" id="avatar-input" accept="image/*" onchange="document.getElementById('avatar-form').submit()">
                    </form>

                    <h2 class="mt-4 text-xl font-semibold text-gray-900">{{ $user->name }}</h2>
                    <p class="text-gray-600 text-sm">{{ $user->email }}</p>
                    
                    @if($user->google_id)
                        <span class="mt-2 px-3 py-1 bg-red-100 text-red-700 text-xs rounded-full flex items-center">
                            <i class="fab fa-google mr-1"></i>
                            Terhubung dengan Google
                        </span>
                    @endif
                </div>

                <!-- Quick Stats -->
                <div class="mt-6 pt-6 border-t border-gray-200 space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Bergabung sejak</span>
                        <span class="font-medium text-gray-900">{{ $user->created_at->format('M Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Total Alamat</span>
                        <span class="font-medium text-gray-900">{{ $addresses->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">
                        <i class="fas fa-user mr-2 text-primary-600"></i>
                        Informasi Pribadi
                    </h3>
                    <button onclick="toggleEdit('personal')" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                        <i class="fas fa-edit mr-1"></i>
                        Edit
                    </button>
                </div>

                <!-- View Mode -->
                <div id="personal-view" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Nama Lengkap</label>
                            <p class="mt-1 text-gray-900">{{ $user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Email</label>
                            <p class="mt-1 text-gray-900">{{ $user->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">No. Telepon</label>
                            <p class="mt-1 text-gray-900">{{ $user->phone ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Edit Mode -->
                <form action="{{ route('profile.update') }}" method="POST" id="personal-edit" class="hidden space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500" required>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">No. Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500" placeholder="08xxxxxxxxxx">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="toggleEdit('personal')" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password -->
            @if(!$user->google_id)
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">
                        <i class="fas fa-lock mr-2 text-primary-600"></i>
                        Ubah Password
                    </h3>
                    <button onclick="toggleEdit('password')" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                        <i class="fas fa-edit mr-1"></i>
                        Edit
                    </button>
                </div>

                <div id="password-view" class="text-gray-600">
                    <p>Password terakhir diubah: {{ $user->updated_at->diffForHumans() }}</p>
                </div>

                <form action="{{ route('profile.password') }}" method="POST" id="password-edit" class="hidden space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password Saat Ini</label>
                        <input type="password" name="current_password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500" required>
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password Baru</label>
                        <input type="password" name="password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500" required>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500" required>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="toggleEdit('password')" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>
            @endif

            <!-- Addresses -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">
                        <i class="fas fa-map-marker-alt mr-2 text-primary-600"></i>
                        Alamat Pengiriman
                    </h3>
                    <button onclick="showAddressModal()" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 text-sm">
                        <i class="fas fa-plus mr-1"></i>
                        Tambah Alamat
                    </button>
                </div>

                @if($addresses->isEmpty())
                    <div class="text-center py-12">
                        <i class="fas fa-map-marker-alt text-5xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Belum ada alamat tersimpan</p>
                        <button onclick="showAddressModal()" class="mt-4 text-primary-600 hover:text-primary-700 font-medium">
                            Tambah Alamat Pertama
                        </button>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($addresses as $address)
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-primary-300 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="font-semibold text-gray-900">{{ $address->label }}</span>
                                        @if($address->is_default)
                                            <span class="px-2 py-1 bg-primary-100 text-primary-700 text-xs rounded-full">
                                                Utama
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-gray-900 font-medium">{{ $address->recipient_name }}</p>
                                    <p class="text-gray-600 text-sm">{{ $address->phone }}</p>
                                    <p class="text-gray-600 text-sm mt-2">{{ $address->full_address }}</p>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="editAddress({{ $address->id }})" class="text-primary-600 hover:text-primary-700 p-2">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="confirmDeleteAddress({{ $address->id }})" class="text-red-600 hover:text-red-700 p-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Address Modal -->
<div id="address-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Tambah Alamat Baru</h3>
            <button onclick="closeAddressModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="address-form" action="{{ route('profile.address.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" value="POST" id="address-method">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Label Alamat</label>
                    <input type="text" name="label" id="label" placeholder="Rumah, Kantor, dll" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Penerima</label>
                    <input type="text" name="recipient_name" id="recipient_name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500" required>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">No. Telepon</label>
                <input type="text" name="phone" id="address_phone" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                <textarea name="address" id="address_text" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500" required></textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kota</label>
                    <input type="text" name="city" id="city" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Provinsi</label>
                    <input type="text" name="province" id="province" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kode Pos</label>
                    <input type="text" name="postal_code" id="postal_code" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500" required>
                </div>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" name="is_default" id="is_default" value="1" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                <label for="is_default" class="ml-2 block text-sm text-gray-900">
                    Jadikan alamat utama
                </label>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button type="button" onclick="closeAddressModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleEdit(section) {
        const view = document.getElementById(section + '-view');
        const edit = document.getElementById(section + '-edit');
        
        if (view.classList.contains('hidden')) {
            view.classList.remove('hidden');
            edit.classList.add('hidden');
        } else {
            view.classList.add('hidden');
            edit.classList.remove('hidden');
        }
    }

    function showAddressModal() {
        document.getElementById('modal-title').textContent = 'Tambah Alamat Baru';
        document.getElementById('address-form').action = '{{ route("profile.address.store") }}';
        document.getElementById('address-method').value = 'POST';
        document.getElementById('address-form').reset();
        document.getElementById('address-modal').classList.remove('hidden');
    }

    function closeAddressModal() {
        document.getElementById('address-modal').classList.add('hidden');
    }

    function editAddress(id) {
        Swal.fire({
            icon: 'info',
            title: 'Fitur Segera Hadir',
            text: 'Fitur edit alamat akan segera tersedia. Untuk saat ini, silakan hapus dan tambah ulang.',
            confirmButtonColor: '#ea580c'
        });
    }

    function confirmDeleteAddress(addressId) {
        Swal.fire({
            title: 'Hapus Alamat?',
            text: 'Alamat akan dihapus secara permanen',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Create and submit form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/profile/address/${addressId}`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Close modal when clicking outside
    document.getElementById('address-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAddressModal();
        }
    });
</script>
@endpush
@endsection
