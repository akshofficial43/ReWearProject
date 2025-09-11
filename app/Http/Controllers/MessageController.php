<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    // Show conversations list
    public function index()
    {
        $currentUser = Auth::user();
        
        // Get unique conversations grouped by product and other user
        $conversations = DB::select("
            SELECT 
                u.userId, u.name, u.profile_image,
                p.productId, p.name as productName, 
                MAX(m.created_at) as last_message_time,
                (SELECT content FROM messages 
                 WHERE ((sender_id = ? AND receiver_id = u.userId) OR (sender_id = u.userId AND receiver_id = ?))
                 AND product_id = p.productId
                 ORDER BY created_at DESC LIMIT 1) as last_message,
                (SELECT COUNT(*) FROM messages 
                 WHERE receiver_id = ? AND sender_id = u.userId AND product_id = p.productId AND `read` = 0) as unread_count
            FROM messages m
            JOIN users u ON (m.sender_id = u.userId AND m.receiver_id = ?) OR (m.receiver_id = u.userId AND m.sender_id = ?)
            JOIN products p ON m.product_id = p.productId
            WHERE m.sender_id = ? OR m.receiver_id = ?
            GROUP BY u.userId, u.name, u.profile_image, p.productId, p.name
            ORDER BY last_message_time DESC
        ", [
            $currentUser->userId, $currentUser->userId,
            $currentUser->userId, 
            $currentUser->userId, $currentUser->userId,
            $currentUser->userId, $currentUser->userId
        ]);

        // Check for query params to start a new conversation
        $selectedProduct = null;
        $selectedUser = null;
        
        if (request()->has('product_id') && request()->has('seller_id')) {
            $productId = request()->get('product_id');
            $sellerId = request()->get('seller_id');
            
            $selectedProduct = Product::find($productId);
            $selectedUser = User::find($sellerId);
        }
        
        return view('messages.index', compact('conversations', 'selectedProduct', 'selectedUser'));
    }
    
    // Show a specific conversation
    public function show($productId, $userId)
    {
        $currentUser = Auth::user();
        $otherUser = User::findOrFail($userId);
        $product = Product::findOrFail($productId);
        
        // Get messages between the users for this product
        $messages = Message::where(function($query) use ($currentUser, $otherUser, $productId) {
            $query->where('sender_id', $currentUser->userId)
                  ->where('receiver_id', $otherUser->userId)
                  ->where('product_id', $productId);
        })->orWhere(function($query) use ($currentUser, $otherUser, $productId) {
            $query->where('sender_id', $otherUser->userId)
                  ->where('receiver_id', $currentUser->userId)
                  ->where('product_id', $productId);
        })->orderBy('created_at', 'asc')->get();
        
        // Mark messages as read - also using proper escaping with the DB facade
        Message::where('sender_id', $otherUser->userId)
              ->where('receiver_id', $currentUser->userId)
              ->where('product_id', $productId)
              ->where('read', false)
              ->update(['read' => true]);
        
        return view('messages.show', compact('messages', 'otherUser', 'product'));
    }
    
    // Store a new message in an existing conversation
    public function store(Request $request, $productId, $userId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);
        
        $currentUser = Auth::user();
        $receiver = User::findOrFail($userId);
        
        $message = new Message([
            'sender_id' => $currentUser->userId,
            'receiver_id' => $receiver->userId,
            'product_id' => $productId, // Ensure this is being set
            'content' => $request->content,
            'read' => false
        ]);
        
        $message->save();
        
        return redirect()->route('messages.show', [$productId, $userId])
                        ->with('success', 'Message sent successfully');
    }
    
    // Start a new conversation
    public function startConversation(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,productId',
            'receiver_id' => 'required|exists:users,userId',
            'content' => 'required|string|max:1000',
        ]);
        
        $currentUser = Auth::user();
        $receiver = User::findOrFail($request->receiver_id);
        $product = Product::findOrFail($request->product_id);
        
        // Don't allow messaging yourself
        if ($currentUser->userId == $receiver->userId) {
            return redirect()->back()->with('error', 'You cannot message yourself.');
        }
        
        $message = new Message([
            'sender_id' => $currentUser->userId,
            'receiver_id' => $receiver->userId,
            'product_id' => $request->product_id,
            'content' => $request->content,
            'read' => false
        ]);
        
        $message->save();
        
        return redirect()->route('messages.show', [$request->product_id, $request->receiver_id])
                        ->with('success', 'Message sent successfully');
    }
    
    // Get count of unread messages for the current user
    public function getUnreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
                        ->where('read', false)
                        ->count();
                        
        return response()->json(['count' => $count]);
    }
}