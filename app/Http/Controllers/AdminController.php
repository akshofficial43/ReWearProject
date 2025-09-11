<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    $this->middleware(\App\Http\Middleware\AdminMiddleware::class);
    }
    
    /**
     * Show the admin dashboard with statistics.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $recentOrders = Order::with('user')->latest()->take(5)->get();
        
    // Calculate revenue from completed payments of delivered orders
    $totalRevenue = Payment::whereHas('order', function ($q) {
        $q->where('status', 'delivered');
        })
        ->where('payment_status', 'completed')
        ->sum('amount');
        
        // New users this month
        $newUsers = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
            
        return view('admin.dashboard', compact(
            'totalUsers',
            'totalProducts',
            'totalOrders',
            'recentOrders',
            'pendingOrders',
            'totalRevenue',
            'newUsers'
        ));
    }
    
    /**
     * Display a listing of all users.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function users(Request $request)
    {
        $query = User::query()->withCount('products');
        
        // Handle search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Handle role filter
        if ($request->has('role') && !empty($request->role)) {
            $query->where('role', $request->role);
        }
        
    $users = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show form to create a new user.
     */
    public function createUser()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user.
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,user',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }
    
    /**
     * Show the form for editing the specified user.
     *
     * @param int $userId
     * @return \Illuminate\View\View
     */
    public function editUser($userId)
    {
        $user = User::findOrFail($userId);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Display the specified user.
     */
    public function showUser($userId)
    {
        $user = User::findOrFail($userId);
        return view('admin.users.show', compact('user'));
    }
    
    /**
     * Update the specified user in database.
     *
     * @param Request $request
     * @param int $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUser(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId . ',userId',
            'role' => 'required|in:admin,user',
            'address' => 'nullable|string',
            'password' => 'nullable|min:8|confirmed',
            'profile_image' => 'nullable|image|max:2048',
        ]);
        
        // Handle profile image upload if provided
        if ($request->hasFile('profile_image')) {
            // Delete old profile image if exists
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            
            $path = $request->file('profile_image')->store('profile-images', 'public');
            $user->profile_image = $path;
        }
        
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        
        if ($request->filled('address')) {
            $user->address = $request->address;
        }
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();
        
    return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully');
    }
    
    /**
     * Remove the specified user from database.
     *
     * @param int $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);
        
        // Don't allow admin to delete themselves
        if ($user->userId === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account');
        }
        
        // Handle related records before deletion (optional)
        // $user->products()->update(['status' => 'unavailable']);
        
        // Delete user's profile image if exists
        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }
        
        $user->delete();
        
    return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully');
    }
    
    /**
     * Display a listing of all products.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function products(Request $request)
    {
        $query = Product::with('user', 'category', 'images');
        
        // Handle search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }
        
        // Handle category filter
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('categoryId', $request->category_id);
        }
        
        // Handle status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        $products = $query->orderBy('created_at', 'desc')->paginate(15);
        $categories = Category::all();
        
        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Remove a product (admin) along with its images from storage.
     *
     * @param int $productId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyProduct($productId)
    {
        $product = Product::with('images')
            ->where('productId', $productId)
            ->firstOrFail();

        // Optional guard: don't allow deleting products tied to delivered orders
        if ($product->orderItems()->whereHas('order', function ($q) {
            $q->where('status', 'delivered');
        })->exists()) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Cannot delete a product that was part of a delivered order.');
        }

        // Delete related images (DB + storage)
        foreach ($product->images as $image) {
            if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
        }

        // Delete main image if present
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully');
    }
    
    /**
     * Display a listing of all orders.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function orders(Request $request)
    {
        // Base filtered query (reused for listing + stats)
        $base = Order::query();

        // Handle status filter
        if ($request->filled('status')) {
            $base->where('status', $request->status);
        }

        // Handle date range filter
        if ($request->filled('date_start')) {
            $base->whereDate('created_at', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $base->whereDate('created_at', '<=', $request->date_end);
        }

        // Listing with eager loads to avoid N+1
        $orders = (clone $base)
            ->with(['user', 'payment', 'items.product.images'])
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Stats: totals and per-status counts
        $totalOrders = (clone $base)->count();
        $statusCounts = (clone $base)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // Revenue: completed payments scoped by filters and delivered by default
        $revenueStatus = $request->filled('status') ? $request->status : 'delivered';
        $revenue = Payment::query()
            ->where('payment_status', 'completed')
            ->whereHas('order', function ($q) use ($request, $revenueStatus) {
                $q->when($request->filled('date_start'), fn($qq) => $qq->whereDate('created_at', '>=', $request->date_start))
                  ->when($request->filled('date_end'), fn($qq) => $qq->whereDate('created_at', '<=', $request->date_end))
                  ->when($revenueStatus, fn($qq) => $qq->where('status', $revenueStatus));
            })
            ->sum('amount');

        return view('admin.orders.index', compact('orders', 'totalOrders', 'statusCounts', 'revenue'));
    }
    
    /**
     * Display the specified order.
     *
     * @param int $orderId
     * @return \Illuminate\View\View
     */
    public function viewOrder($orderId)
    {
        $order = Order::with(['items.product', 'user', 'payment', 'shipping'])->findOrFail($orderId);
        return view('admin.orders.show', compact('order'));
    }
    
    /**
     * Update the order status.
     *
     * @param Request $request
     * @param int $orderId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateOrderStatus(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);
        
        $oldStatus = $order->status;
        $order->status = $request->status;
        $order->save();
        
        // Update product status if order is delivered
        if ($request->status === 'delivered' && $oldStatus !== 'delivered') {
            foreach ($order->items as $item) {
                $product = $item->product;
                // Check if the method exists
                if (method_exists($product, 'markAsSold')) {
                    $product->markAsSold();
                } else {
                    $product->status = 'sold';
                    $product->save();
                }
            }
        } elseif ($request->status === 'cancelled' && $oldStatus !== 'cancelled') {
            foreach ($order->items as $item) {
                $product = $item->product;
                $product->status = 'available';
                $product->save();
            }
        }
        
        return redirect()->back()->with('success', 'Order status updated successfully');
    }
    
    /**
     * Display a listing of all categories.
     *
     * @return \Illuminate\View\View
     */
    public function categories()
    {
        $categories = Category::withCount('products')->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }
    
    /**
     * Show the form for creating a new category.
     *
     * @return \Illuminate\View\View
     */
    public function createCategory()
    {
        return view('admin.categories.create');
    }
    
    /**
     * Store a newly created category in database.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
        ]);
        
        Category::create([
            'name' => $request->name,
            'description' => $request->description ?? null,
        ]);
        
    return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully');
    }
    
    /**
     * Show the form for editing the specified category.
     *
     * @param int $categoryId
     * @return \Illuminate\View\View
     */
    public function editCategory($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        return view('admin.categories.edit', compact('category'));
    }
    
    /**
     * Update the specified category in database.
     *
     * @param Request $request
     * @param int $categoryId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateCategory(Request $request, $categoryId)
    {
        $category = Category::findOrFail($categoryId);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $categoryId . ',categoryId',
            'description' => 'nullable|string|max:1000',
        ]);
        
        $category->update([
            'name' => $request->name,
            'description' => $request->description ?? $category->description,
        ]);
        
    return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully');
    }
    
    /**
     * Remove the specified category from database.
     *
     * @param int $categoryId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteCategory($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        
        // Check if category has products
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories')
                ->with('error', 'Cannot delete category with associated products');
        }
        
        $category->delete();
        
    return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully');
    }
    
    /**
     * Display sales and product reports.
     *
     * @return \Illuminate\View\View
     */
    public function reports()
    {
        // Monthly sales for current year
        $monthlySales = Order::selectRaw('MONTH(created_at) as month, COUNT(*) as count, SUM(total) as total')
            ->whereYear('created_at', date('Y'))
            ->where('status', 'delivered')
            ->groupBy('month')
            ->get();
            
        // Top selling products
        $topProducts = Product::withCount(['orderItems as sales_count' => function($query) {
                $query->whereHas('order', function($q) {
                    $q->where('status', 'delivered');
                });
            }])
            ->orderByDesc('sales_count')
            ->take(10)
            ->get();
            
        // User registration by month
        $userRegistrations = User::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->get();
            
        return view('admin.reports', compact('monthlySales', 'topProducts', 'userRegistrations'));
    }

    /**
     * Show admin's own profile page.
     */
    public function adminProfile()
    {
        $admin = auth()->user();
        return view('admin.profile', compact('admin'));
    }

    /**
     * Update admin's own profile.
     */
    public function updateAdminProfile(Request $request)
    {
        $admin = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->userId . ',userId',
            'password' => 'nullable|min:8|confirmed',
            'profile_image' => 'nullable|image|max:2048',
            'avatar_choice' => 'nullable|string',
        ]);

        if ($request->hasFile('profile_image')) {
            if ($admin->profile_image) {
                Storage::disk('public')->delete($admin->profile_image);
            }
            $path = $request->file('profile_image')->store('profile-images', 'public');
            $admin->profile_image = $path;
        } elseif ($request->filled('avatar_choice')) {
            // Generate an SVG initials avatar using chosen color
            $color = $request->input('avatar_choice');
            $name = trim($request->input('name', $admin->name));
            $parts = preg_split('/\s+/', $name);
            $initials = '';
            foreach ($parts as $p) {
                if ($p !== '') { $initials .= mb_strtoupper(mb_substr($p, 0, 1)); }
                if (mb_strlen($initials) >= 2) break;
            }
            $initials = $initials ?: 'A';
            $svg = "<svg xmlns='http://www.w3.org/2000/svg' width='256' height='256'>".
                   "<rect width='100%' height='100%' rx='128' fill='".htmlspecialchars($color, ENT_QUOTES)."'/>".
                   "<text x='50%' y='54%' dominant-baseline='middle' text-anchor='middle' font-family='Segoe UI, Roboto, Arial' font-size='120' font-weight='800' fill='white'>".
                   htmlspecialchars($initials, ENT_QUOTES)."</text></svg>";

            if ($admin->profile_image) {
                Storage::disk('public')->delete($admin->profile_image);
            }
            $fileName = 'profile-images/avatar-'.$admin->userId.'-'.time().'.svg';
            Storage::disk('public')->put($fileName, $svg);
            $admin->profile_image = $fileName;
        }

        $admin->name = $request->name;
        $admin->email = $request->email;
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }
        $admin->save();

        return redirect()->route('admin.profile.show')->with('success', 'Profile updated successfully');
    }
    
    /**
     * Create and manage ReWear official products.
     *
     * @return \Illuminate\View\View
     */
    public function officialProducts()
    {
        $products = Product::where('is_official', true)
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.official-products.index', compact('products'));
    }
    
    /**
     * Show the form for creating a new official product.
     *
     * @return \Illuminate\View\View
     */
    public function createOfficialProduct()
    {
        $categories = Category::all();
        return view('admin.official-products.create', compact('categories'));
    }
    
    /**
     * Store a newly created official product in database.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeOfficialProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,categoryId',
            'condition' => 'required|in:new,like_new,good,fair,poor',
            'images' => 'required|array|min:1|max:5',
            'images.*' => 'image|max:2048',
        ]);
        
        $imageFiles = $request->file('images');
        
        // Get the admin user
        $adminUser = auth()->user();
        
        $product = new Product();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->categoryId = $request->category_id;
        $product->userId = $adminUser->userId; // Admin is the seller
        $product->status = 'available';
        $product->condition = $request->condition;
        // Save first image as main image path
        $mainImagePath = $imageFiles[0]->store('product-images', 'public');
        $product->image = $mainImagePath;
    $product->location = 'Rewear Official';
        $product->is_official = true; // Mark as official ReWear product
        $product->save();
        
        // Save additional images
        foreach ($imageFiles as $file) {
            $path = $file->store('product-images', 'public');
            \App\Models\ProductImage::create([
                'productId' => $product->productId,
                'image_path' => $path,
            ]);
        }
        
    return redirect()->route('admin.official-products.index')
            ->with('success', 'Official product created successfully');
    }

    /**
     * Remove the specified official product from storage.
     *
     * @param int $productId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyOfficialProduct($productId)
    {
        $product = Product::where('productId', $productId)->where('is_official', true)->firstOrFail();

        // Delete related images from storage and DB
        foreach ($product->images as $image) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($image->image_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
        }

        // Delete main image if present
        if ($product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.official-products.index')->with('success', 'Official product deleted successfully');
    }

    /**
     * Show the form for editing the specified official product.
     */
    public function editOfficialProduct($productId)
    {
        $product = Product::where('productId', $productId)
            ->where('is_official', true)
            ->with('images', 'category')
            ->firstOrFail();
        $categories = Category::all();
        return view('admin.official-products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified official product in storage.
     */
    public function updateOfficialProduct(Request $request, $productId)
    {
        $product = Product::where('productId', $productId)->where('is_official', true)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,categoryId',
            'condition' => 'required|in:new,like_new,good,fair,poor',
            'images' => 'sometimes|array|max:5',
            'images.*' => 'image|max:2048',
        ]);

        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->categoryId = $request->category_id;
        $product->condition = $request->condition;
        $product->location = 'Rewear Official';
        $product->is_official = true;

        // If new images provided, append them; also set main image if missing
        if ($request->hasFile('images')) {
            $files = $request->file('images');
            foreach ($files as $file) {
                $path = $file->store('product-images', 'public');
                \App\Models\ProductImage::create([
                    'productId' => $product->productId,
                    'image_path' => $path,
                ]);
                if (!$product->image) {
                    $product->image = $path;
                }
            }
        }

        $product->save();

        return redirect()->route('admin.official-products.index')->with('success', 'Official product updated successfully');
    }
}