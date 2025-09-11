<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'userId';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'role',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin()
{
    return $this->role === 'admin';
}

    /**
     * Get the profile image URL.
     *
     * @return string
     */
    public function getProfileImageUrl()
    {
        if ($this->profile_image) {
            return asset('storage/' . $this->profile_image);
        }
        
        return asset('images/default-avatar.png');
    }

    /**
     * Get the products that belong to the user.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'userId');
    }

    /**
     * Get the orders that belong to the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'userId');
    }

    /**
     * Get the messages sent by the user.
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id', 'userId');
    }

    /**
     * Get the messages received by the user.
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id', 'userId');
    }

    /**
     * Get all messages for the user (both sent and received).
     */
    public function messages()
    {
        // Updated to use snake_case column names (sender_id, receiver_id)
        return Message::where('sender_id', $this->userId)
            ->orWhere('receiver_id', $this->userId);
    }

    /**
     * Get the favorites that belong to the user.
     */
    public function favorites()
    {
        // Check if Favorite model exists before trying to query it
        if (class_exists('App\Models\Favorite')) {
            return $this->hasMany(Favorite::class, 'userId');
        }
        
        return null;
    }

    public function cart()
    {
        return $this->hasOne(Cart::class, 'userId', 'userId');
    }
}