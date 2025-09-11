<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProductRequest;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        // All authenticated users can now access product management functions
    }

    public function index(Request $request)
    {
        $query = Product::query()->where('status', 'available');

        // Category filters: support single 'category' or multiple 'categories[]'
        if ($request->filled('categories')) {
            $query->whereIn('categoryId', (array) $request->categories);
        } elseif ($request->filled('category')) {
            $query->where('categoryId', $request->category);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhereHas('category', function($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Condition filter: support single value or array
        if ($request->has('condition')) {
            $condition = $request->condition;
            if (is_array($condition)) {
                $query->whereIn('condition', $condition);
            } else {
                $query->where('condition', $condition);
            }
        }

    if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

    if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Optional: location filter
    if ($request->filled('location')) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $query->latest();
                    break;
                case 'oldest':
                    $query->oldest();
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }

    $products = $query->with(['images', 'category', 'user'])->paginate(12);
        $categories = Category::all();
        
        // Group categories for display
        $categoryGroups = Category::where('parent_id', null)
            ->with('children')
            ->get();
            
        return view('products.index', compact('products', 'categories', 'categoryGroups'));
    }

    public function create()
    {
        // Check if there are any categories
        $categories = Category::all();
        
        // If no categories, create default ones
        if($categories->isEmpty()) {
            // Create main categories first
            $mainCategories = [
                ['name' => 'Electronics & Appliances', 'description' => 'Electronic devices and home appliances'],
                ['name' => 'Vehicles', 'description' => 'Cars, bikes, and other vehicles'],
                ['name' => 'Home & Furniture', 'description' => 'Home decor, furniture and household items'],
                ['name' => 'Fashion', 'description' => 'Clothing, accessories, and footwear'],
                ['name' => 'Books, Sports & Hobbies', 'description' => 'Books, sports equipment, and hobby items']
            ];
            
            $mainCategoryIds = [];
            
            foreach ($mainCategories as $category) {
                $newCategory = Category::create([
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'status' => 'approved',
                    'parent_id' => null
                ]);
                
                $mainCategoryIds[$newCategory->name] = $newCategory->categoryId;
            }
            
            // Create subcategories
            $subcategories = [
                // Electronics & Appliances
                ['name' => 'Mobiles & Accessories', 'description' => 'Mobile phones and accessories', 'parent' => 'Electronics & Appliances'],
                ['name' => 'Computers & Laptops', 'description' => 'Desktop computers and laptops', 'parent' => 'Electronics & Appliances'],
                ['name' => 'TV, Audio & Video', 'description' => 'Television, audio and video equipment', 'parent' => 'Electronics & Appliances'],
                ['name' => 'Home Appliances', 'description' => 'Kitchen and home appliances', 'parent' => 'Electronics & Appliances'],
                
                // Vehicles
                ['name' => 'Cars', 'description' => 'New and used cars', 'parent' => 'Vehicles'],
                ['name' => 'Bikes & Scooters', 'description' => 'Motorcycles and scooters', 'parent' => 'Vehicles'],
                ['name' => 'Commercial Vehicles', 'description' => 'Trucks, vans and commercial vehicles', 'parent' => 'Vehicles'],
                ['name' => 'Spare Parts', 'description' => 'Vehicle spare parts and accessories', 'parent' => 'Vehicles'],
                
                // Home & Furniture
                ['name' => 'Furniture', 'description' => 'Tables, chairs, beds and more', 'parent' => 'Home & Furniture'],
                ['name' => 'Kitchen Items', 'description' => 'Cookware, dinnerware and kitchen accessories', 'parent' => 'Home & Furniture'],
                ['name' => 'Home Décor', 'description' => 'Decorative items for home', 'parent' => 'Home & Furniture'],
                
                // Fashion
                ['name' => 'Men\'s Clothing & Accessories', 'description' => 'Clothes and accessories for men', 'parent' => 'Fashion'],
                ['name' => 'Women\'s Clothing & Accessories', 'description' => 'Clothes and accessories for women', 'parent' => 'Fashion'],
                ['name' => 'Footwear', 'description' => 'Shoes, sandals and boots', 'parent' => 'Fashion'],
                ['name' => 'Jewelry & Watches', 'description' => 'Jewelry items and watches', 'parent' => 'Fashion'],
                
                // Books, Sports & Hobbies
                ['name' => 'Books & Magazines', 'description' => 'All kinds of books and magazines', 'parent' => 'Books, Sports & Hobbies'],
                ['name' => 'Musical Instruments', 'description' => 'Guitars, pianos and other musical instruments', 'parent' => 'Books, Sports & Hobbies'],
                ['name' => 'Sports Equipment', 'description' => 'Sports and fitness equipment', 'parent' => 'Books, Sports & Hobbies'],
                ['name' => 'Art & Collectibles', 'description' => 'Artwork and collectible items', 'parent' => 'Books, Sports & Hobbies']
            ];
            
            foreach ($subcategories as $subcat) {
                Category::create([
                    'name' => $subcat['name'],
                    'description' => $subcat['description'],
                    'status' => 'approved',
                    'parent_id' => $mainCategoryIds[$subcat['parent']]
                ]);
            }
            
            // Refresh categories
            $categories = Category::all();
        }
        
        // Organize categories in a hierarchical structure for display
        $categoryGroups = Category::where('parent_id', null)
            ->with('children')
            ->get();
            
        return view('products.create', compact('categories', 'categoryGroups'));
    }

    public function store(ProductRequest $request)
    {
        $product = Product::create([
            'userId' => Auth::user()->userId,
            'categoryId' => $request->categoryId,
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'condition' => $request->condition,
            'status' => 'available',
            'location' => $request->location,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('product_images', 'public');
                ProductImage::create([
                    'productId' => $product->productId,
                    'image_path' => $imagePath
                ]);
            }
        }

        return redirect()->route('products.show', $product->productId)
            ->with('success', 'Product listed successfully!');
    }

    public function show($productId)
    {
        $product = Product::with(['images', 'category', 'user', 'reviews.user'])->findOrFail($productId);
        
        // Get similar products
        $similarProducts = Product::where('categoryId', $product->categoryId)
            ->where('productId', '!=', $product->productId)
            ->where('status', 'available')
            ->take(4)
            ->get();
        
        // Get categories for sidebar
        $categoryGroups = Category::where('parent_id', null)
            ->with('children')
            ->get();
            
        return view('products.show', compact('product', 'similarProducts', 'categoryGroups'));
    }

    public function edit($productId)
    {
        $product = Product::findOrFail($productId);
        
        $this->authorize('update', $product);
        
        $categories = Category::all();
        
        // Organize categories for display
        $categoryGroups = Category::where('parent_id', null)
            ->with('children')
            ->get();
            
        return view('products.edit', compact('product', 'categories', 'categoryGroups'));
    }

    public function update(ProductRequest $request, $productId)
    {
        $product = Product::findOrFail($productId);
        
        $this->authorize('update', $product);

        $product->update([
            'categoryId' => $request->categoryId,
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'condition' => $request->condition,
            'status' => $request->status,
            'location' => $request->location,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('product_images', 'public');
                ProductImage::create([
                    'productId' => $product->productId,
                    'image_path' => $imagePath
                ]);
            }
        }

        // Remove images if requested
        if ($request->has('remove_images')) {
            foreach ((array)$request->remove_images as $imageId) {
                $image = ProductImage::find($imageId);
                if ($image && (string)$image->productId === (string)$product->productId) {
                    if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                        Storage::disk('public')->delete($image->image_path);
                    }
                    $image->delete();
                }
            }
            // Reload relation in case the view expects fresh images
            $product->load('images');
        }

        return redirect()->route('products.show', $product->productId)
            ->with('success', 'Product updated successfully!');
    }

    public function destroy($productId)
    {
        $product = Product::findOrFail($productId);
        
        $this->authorize('delete', $product);
        
        // Delete associated images from storage
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        
        $product->delete();
        
        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully!');
    }
    
    public function addReview(Request $request, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string'
        ]);
        
        $product = Product::findOrFail($productId);
        
        // Check if user has already reviewed this product
        $existingReview = Review::where('userId', Auth::id())
            ->where('productId', $productId)
            ->first();
            
        if ($existingReview) {
            return redirect()->back()
                ->with('error', 'You have already reviewed this product.');
        }
        
        Review::create([
            'userId' => Auth::id(),
            'productId' => $productId,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);
        
        return redirect()->route('products.show', $productId)
            ->with('success', 'Review added successfully!');
    }
    
    /**
     * Advanced search with filters
     */
    public function search(Request $request)
    {
        $query = Product::query()->where('status', 'available');
        
        // Search in multiple fields
        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('location', 'like', "%{$searchTerm}%")
                  ->orWhereHas('category', function($categoryQuery) use ($searchTerm) {
                      $categoryQuery->where('name', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('user', function($userQuery) use ($searchTerm) {
                      $userQuery->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }
        
        // Category filter: support single 'category' or multiple 'categories[]'
        if ($request->filled('categories')) {
            $query->whereIn('categoryId', (array) $request->categories);
        } elseif ($request->filled('category')) {
            $query->where('categoryId', $request->category);
        }
        
        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        
        // Condition filter
        if ($request->filled('condition')) {
            $query->whereIn('condition', (array)$request->condition);
        }
        
        // Location filter
        if ($request->filled('location')) {
            $query->where('location', 'like', "%{$request->location}%");
        }
        
        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'oldest':
                $query->oldest();
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            default:
                $query->latest();
        }
        
        $products = $query->with(['images', 'category', 'user'])->paginate(12);
        $categories = Category::all();
        
        // Get search statistics
        $searchStats = [
            'total_results' => $products->total(),
            'search_term' => $request->q,
            'filters_applied' => $this->getAppliedFilters($request)
        ];
        
        return view('products.search', compact('products', 'categories', 'searchStats'));
    }
    
    /**
     * Autocomplete search suggestions
     */
    public function searchSuggestions(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        // Get product name suggestions
        $productSuggestions = Product::where('status', 'available')
            ->where('name', 'like', "%{$query}%")
            ->select('name')
            ->distinct()
            ->limit(5)
            ->pluck('name');
        
        // Get category suggestions
        $categorySuggestions = Category::where('name', 'like', "%{$query}%")
            ->select('name')
            ->limit(3)
            ->pluck('name');
        
        // Get location suggestions
        $locationSuggestions = Product::where('status', 'available')
            ->where('location', 'like', "%{$query}%")
            ->select('location')
            ->distinct()
            ->limit(3)
            ->pluck('location');
        
        $suggestions = [
            'products' => $productSuggestions,
            'categories' => $categorySuggestions,
            'locations' => $locationSuggestions
        ];
        
        return response()->json($suggestions);
    }
    
    /**
     * Get popular search terms
     */
    public function popularSearches()
    {
        // This would typically come from a search log table
        // For now, return some common searches based on available products
        $popularCategories = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->limit(8)
            ->pluck('name');
            
        $popularLocations = Product::where('status', 'available')
            ->select('location')
            ->groupBy('location')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(5)
            ->pluck('location');
        
        return response()->json([
            'categories' => $popularCategories,
            'locations' => $popularLocations
        ]);
    }
    
    /**
     * Get applied filters for display
     */
    private function getAppliedFilters(Request $request)
    {
        $filters = [];
        
        if ($request->filled('category')) {
            $category = Category::find($request->category);
            if ($category) {
                $filters[] = "Category: {$category->name}";
            }
        }
        
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $minPrice = $request->min_price ?: '0';
            $maxPrice = $request->max_price ?: '∞';
            $filters[] = "Price: ${$minPrice} - ${$maxPrice}";
        }
        
        if ($request->filled('condition')) {
            $conditions = implode(', ', (array)$request->condition);
            $filters[] = "Condition: {$conditions}";
        }
        
        if ($request->filled('location')) {
            $filters[] = "Location: {$request->location}";
        }
        
        return $filters;
    }
}