<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sellers = User::where('role', 'seller')->orWhere('role', 'admin')->get();
        $categories = Category::all();
        
        // Create some sample products
        $products = [
            [
                'name' => 'Vintage Denim Jacket',
                'description' => 'Classic vintage denim jacket in excellent condition. Size L. Perfect for any casual outfit.',
                'price' => 250,
                'condition' => 'good',
                'category' => 'Men’s Clothing & Accessories',
            ],
            [
                'name' => 'Nike Air Max 90',
                'description' => 'Barely worn Nike Air Max 90 sneakers. Size 10. Original box included.',
                'price' => 800,
                'condition' => 'like_new',
                'category' => 'Men’s Clothing & Accessories',
            ],
            [
                'name' => 'Leather Crossbody Bag',
                'description' => 'Genuine leather crossbody bag with adjustable strap. Multiple compartments for storage.',
                'price' => 300,
                'condition' => 'good',
                'category' => 'Women’s Clothing & Accessories',
            ],
            [
                'name' => 'iPhone 12 Pro',
                'description' => 'Apple iPhone 12 Pro in Space Gray, 128GB. Minor scratches on the back. Includes charger and earphones.',
                'price' => 30000,
                'condition' => 'fair',
                'category' => 'Mobiles & Electronics (Phones, Laptops, Gadgets)',
            ],
            [
                'name' => 'Vintage Vinyl Records Collection',
                'description' => 'Collection of 20 classic rock vinyl records from the 70s and 80s. All in playable condition.',
                'price' => 150,
                'condition' => 'good',
                'category' => 'Books, Sports & Hobbies',
            ],
            [
                'name' => 'Handmade Ceramic Vase',
                'description' => 'Beautiful handmade ceramic vase, perfect for fresh or dried flowers. Height: 12 inches.',
                'price' => 249,
                'condition' => 'new',
                'category' => 'Home Décor & Appliances',
            ],
        ];
        
        foreach ($products as $productData) {
            $seller = $sellers->random();
            $category = $categories->where('name', $productData['category'])->first();

            $product = Product::create([
                'userId' => $seller->userId,
                'categoryId' => $category->categoryId,
                'name' => $productData['name'],
                'description' => $productData['description'],
                'price' => $productData['price'],
                'condition' => $productData['condition'],
                'status' => 'available',
                'location' => 'Rewear Official',
            ]);

            // Use a default image path instead of generating an image
            ProductImage::create([
                'productId' => $product->productId,
                'image_path' => 'product_images/default.png'
            ]);
        }
    }
}