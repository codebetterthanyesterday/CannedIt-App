<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'sale_price',
        'stock_quantity',
        'sku',
        'images',
        'brand',
        'weight',
        'expiry_date',
        'manufacture_date',
        'ingredients',
        'nutritional_info',
        'status',
        'is_featured',
        'is_in_stock',
        'rating',
        'review_count',
        'view_count'
    ];

    protected $casts = [
        'images' => 'array',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'rating' => 'decimal:2',
        'is_featured' => 'boolean',
        'expiry_date' => 'date',
        'manufacture_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = 'CAN-' . strtoupper(Str::random(8));
            }
            // Auto-set is_in_stock based on stock_quantity
            $product->is_in_stock = $product->stock_quantity > 0 ? 1 : 0;
        });
        
        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            // Auto-update is_in_stock when stock_quantity changes
            if ($product->isDirty('stock_quantity')) {
                $product->is_in_stock = $product->stock_quantity > 0 ? 1 : 0;
            }
        });
    }

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // Accessors
    public function getDiscountPercentageAttribute()
    {
        if ($this->sale_price && $this->price > $this->sale_price) {
            return round((($this->price - $this->sale_price) / $this->price) * 100);
        }
        return 0;
    }

    public function getCurrentPriceAttribute()
    {
        return $this->sale_price ?: $this->price;
    }

    public function getMainImageAttribute()
    {
        $images = $this->attributes['images'] ?? null;
        
        // Handle if images is a JSON string
        if (is_string($images)) {
            $images = json_decode($images, true);
        }
        
        if ($images && is_array($images) && count($images) > 0) {
            return asset('storage/products/' . $images[0]);
        }
        return asset('images/default-product.jpg');
    }

    public function getIsOnSaleAttribute()
    {
        return $this->sale_price && $this->sale_price < $this->price;
    }

    public function getIsInStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    // Accessor for stock (maps to stock_quantity)
    public function getStockAttribute()
    {
        return $this->stock_quantity;
    }

    // Accessor for image (returns first image path for storage)
    public function getImageAttribute()
    {
        $images = $this->attributes['images'] ?? null;
        
        // Handle if images is a JSON string
        if (is_string($images)) {
            $images = json_decode($images, true);
        }
        
        if ($images && is_array($images) && count($images) > 0) {
            return 'products/' . $images[0];
        }
        return null;
    }

    // Accessor for first_image (returns full URL)
    public function getFirstImageAttribute()
    {
        $images = $this->attributes['images'] ?? null;
        
        // Handle if images is a JSON string
        if (is_string($images)) {
            $images = json_decode($images, true);
        }
        
        if ($images && is_array($images) && count($images) > 0) {
            return asset('storage/products/' . $images[0]);
        }
        return null;
    }

    // Methods
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function updateRating()
    {
        $avgRating = $this->reviews()->avg('rating');
        $reviewCount = $this->reviews()->count();
        
        $this->update([
            'rating' => $avgRating ?? 0,
            'review_count' => $reviewCount,
        ]);
    }

    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function getReviewCountAttribute()
    {
        return $this->reviews()->count();
    }
}
