<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');
        
        // Search
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
        }
        
        // Filter by category
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        
        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        $products = $query->latest()->paginate(15);
        $categories = Category::all();
        
        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|string|max:50|unique:products,sku',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'required|integer|min:0',
            'brand' => 'nullable|string|max:100',
            'weight' => 'nullable|string|max:50',
            'ingredients' => 'nullable|string',
            'nutritional_info' => 'nullable|string',
            'manufacture_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:manufacture_date',
            'status' => 'required|in:active,inactive',
            'is_featured' => 'nullable|boolean',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']);
        
        // Rename stock to stock_quantity for database
        $validated['stock_quantity'] = $validated['stock'];
        unset($validated['stock']);
        
        // Set is_featured default
        $validated['is_featured'] = $request->has('is_featured') ? 1 : 0;

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('products', $imageName, 'public');
            $validated['images'] = [$imageName]; // Store as array in JSON column
        }
        
        // Remove the single 'image' key as we store in 'images'
        unset($validated['image']);

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan!');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|string|max:50|unique:products,sku,' . $product->id,
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'required|integer|min:0',
            'brand' => 'nullable|string|max:100',
            'weight' => 'nullable|string|max:50',
            'ingredients' => 'nullable|string',
            'nutritional_info' => 'nullable|string',
            'manufacture_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:manufacture_date',
            'status' => 'required|in:active,inactive',
            'is_featured' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']);
        
        // Rename stock to stock_quantity for database
        $validated['stock_quantity'] = $validated['stock'];
        unset($validated['stock']);
        
        // Set is_featured
        $validated['is_featured'] = $request->has('is_featured') ? 1 : 0;

        // Handle new image upload
        if ($request->hasFile('image')) {
            // Delete old images
            if ($product->images && is_array($product->images)) {
                foreach ($product->images as $oldImage) {
                    Storage::disk('public')->delete('products/' . $oldImage);
                }
            }

            $image = $request->file('image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('products', $imageName, 'public');
            $validated['images'] = [$imageName];
        }
        
        // Remove the single 'image' key
        unset($validated['image']);

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        // Delete images
        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete('products/' . $image);
            }
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus!');
    }
}
