@props(['src', 'alt' => 'Product', 'class' => 'w-full h-48 object-cover'])

@php
    $imagePath = $src ? (str_contains($src, 'http') ? $src : asset('storage/' . $src)) : null;
    $hasImage = $imagePath && (str_contains($imagePath, 'http') || file_exists(public_path('storage/' . $src)));
@endphp

@if($hasImage)
    <img src="{{ $imagePath }}" 
         alt="{{ $alt }}" 
         {{ $attributes->merge(['class' => $class]) }}
         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
    <div {{ $attributes->merge(['class' => $class . ' bg-gray-100 items-center justify-center flex-col hidden']) }}>
        <i class="fas fa-box-open text-4xl text-gray-400 mb-2"></i>
        <span class="text-xs text-gray-500">No Image</span>
    </div>
@else
    <div {{ $attributes->merge(['class' => $class . ' bg-gray-100 flex items-center justify-center flex-col']) }}>
        <i class="fas fa-box-open text-4xl text-gray-400 mb-2"></i>
        <span class="text-xs text-gray-500">No Image</span>
    </div>
@endif
