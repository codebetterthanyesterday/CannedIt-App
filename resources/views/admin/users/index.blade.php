@extends('admin.layouts.app')

@section('title', 'Kelola Users')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Kelola Users</h1>
    <p class="text-gray-600 mt-1">Manage pengguna dan hak akses admin</p>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <input type="text" name="search" placeholder="Cari nama atau email..." value="{{ request('search') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
            <select name="is_admin" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                <option value="">Semua User</option>
                <option value="1" {{ request('is_admin') === '1' ? 'selected' : '' }}>Admin Only</option>
                <option value="0" {{ request('is_admin') === '0' ? 'selected' : '' }}>Customer Only</option>
            </select>
        </div>
        <div class="flex space-x-2">
            <button type="submit" class="flex-1 px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800">
                <i class="fas fa-search mr-2"></i>
                Filter
            </button>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Users</p>
                <p class="text-2xl font-bold text-gray-900">{{ $users->total() }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Admin Users</p>
                <p class="text-2xl font-bold text-gray-900">{{ $users->where('is_admin', true)->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-shield-alt text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Customers</p>
                <p class="text-2xl font-bold text-gray-900">{{ $users->where('is_admin', false)->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-user text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Google Users</p>
                <p class="text-2xl font-bold text-gray-900">{{ $users->whereNotNull('google_id')->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fab fa-google text-red-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stats</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bergabung</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($users as $user)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        @php
                            $hasAvatar = $user->avatar && 
                                        !str_contains($user->avatar, 'lh3.googleusercontent.com/a/default') &&
                                        (str_contains($user->avatar, 'googleusercontent.com') || 
                                         file_exists(public_path($user->avatar)));
                        @endphp
                        
                        @if($hasAvatar)
                            <img src="{{ str_contains($user->avatar, 'http') ? $user->avatar : asset($user->avatar) }}" 
                                 alt="{{ $user->name }}" 
                                 class="h-10 w-10 rounded-full object-cover"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 items-center justify-center hidden">
                                <span class="text-xs font-bold text-white">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $user->name)[1] ?? $user->name, 0, 1)) }}
                                </span>
                            </div>
                        @else
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
                                <span class="text-xs font-bold text-white">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $user->name)[1] ?? $user->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                            @if($user->google_id)
                                <div class="flex items-center text-xs text-gray-500">
                                    <i class="fab fa-google text-red-500 mr-1"></i>
                                    Google Account
                                </div>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $user->email }}</div>
                    <div class="text-xs text-gray-500">{{ $user->phone ?? 'No phone' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-xs text-gray-500">
                        <div><i class="fas fa-shopping-cart mr-1"></i>{{ $user->orders_count }} orders</div>
                        <div><i class="fas fa-star mr-1"></i>{{ $user->reviews_count }} reviews</div>
                        <div><i class="fas fa-heart mr-1"></i>{{ $user->wishlists_count }} wishlist</div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($user->is_admin)
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                            <i class="fas fa-shield-alt mr-1"></i> Admin
                        </span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            <i class="fas fa-user mr-1"></i> Customer
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $user->created_at->format('d M Y') }}
                    <div class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    @if(Auth::id() !== $user->id)
                        <button onclick="confirmToggleAdmin({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->is_admin ? 'true' : 'false' }})" 
                                class="text-primary-600 hover:text-primary-900">
                            @if($user->is_admin)
                                <i class="fas fa-user-minus"></i> Revoke Admin
                            @else
                                <i class="fas fa-user-shield"></i> Make Admin
                            @endif
                        </button>
                    @else
                        <span class="text-gray-400 italic">You</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                    <i class="fas fa-users text-4xl mb-3"></i>
                    <p>Tidak ada user ditemukan</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $users->links() }}
</div>

@push('scripts')
<script>
function confirmToggleAdmin(userId, userName, isCurrentlyAdmin) {
    const action = isCurrentlyAdmin ? 'Cabut Status Admin' : 'Jadikan Admin';
    const actionText = isCurrentlyAdmin ? 'cabut status admin dari' : 'jadikan';
    const icon = isCurrentlyAdmin ? 'warning' : 'question';
    
    Swal.fire({
        title: `${action}?`,
        html: `Yakin ingin ${actionText} <strong>${userName}</strong> ${isCurrentlyAdmin ? '' : 'sebagai admin'}?`,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: isCurrentlyAdmin ? '#ef4444' : '#ea580c',
        cancelButtonColor: '#6b7280',
        confirmButtonText: `Ya, ${action}!`,
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Create and submit form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/users/${userId}/toggle-admin`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'PATCH';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
