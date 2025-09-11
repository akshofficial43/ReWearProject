<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Show user profile page
     */
    public function profile()
    {
        // Add debug info to check if this method is being called
        \Log::info('Profile method called');
        
        // Force Laravel to look for the view in the correct location
        $viewPath = resource_path('views/user/profile.blade.php');
        \Log::info('Looking for view at: ' . $viewPath);
        
        if (!file_exists($viewPath)) {
            \Log::error('View file does not exist at: ' . $viewPath);
            // Create a very basic view on-the-fly if it doesn't exist
            if (!is_dir(resource_path('views/user'))) {
                mkdir(resource_path('views/user'), 0755, true);
            }
            file_put_contents($viewPath, '<h1>Profile Page</h1><p>Hello, {{ Auth::user()->name }}</p>');
            \Log::info('Created basic view file');
        }
        
        return view('user.profile');
    }

    /**
     * Show edit profile form
     */
    public function edit()
    {
        $user = Auth::user();
        // Parse address data if it's stored as JSON
        if ($user->address && $this->isJson($user->address)) {
            $user->addressData = json_decode($user->address, true);
        }
        return view('user.edit', compact('user'));
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Fix: Change 'user' to 'users' (plural table name)
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->userId, 'userId')],
            'phone' => ['nullable', 'string', 'max:20'],
            'street' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'zip' => ['nullable', 'string', 'max:20'],
            'profile_image' => ['nullable', 'image', 'max:1024'],
        ]);
        
        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old profile image if exists
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            
            $path = $request->file('profile_image')->store('profile-images', 'public');
            $user->profile_image = $path;
        }
        
        // Create address JSON from form fields
        $addressData = [
            'phone' => $validated['phone'] ?? null,
            'street' => $validated['street'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'zip' => $validated['zip'] ?? null,
        ];
        
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->address = json_encode($addressData);
        $user->save();
        
        return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
    }

    /**
     * Show change password form
     */
    public function changePasswordForm()
    {
        return view('user.change-password');
    }

    /**
     * Update user password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        $user = Auth::user();
        
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }
        
        $user->password = Hash::make($request->password);
        $user->save();
        
        return redirect()->route('profile.show')->with('success', 'Password changed successfully!');
    }
    
    /**
     * Check if a string is valid JSON
     */
    private function isJson($string) {
        if (!is_string($string)) return false;
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
    
    /**
     * Remove user profile image
     */
    public function removeProfileImage()
    {
        try {
            $user = Auth::user();
            
            // Delete the old profile image if it exists
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            
            // Clear the profile image from user record
            $user->profile_image = null;
            $user->save();
            
            return redirect()->route('profile.edit')->with('success', 'Profile image removed successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Error removing profile image: ' . $e->getMessage());
            return redirect()->route('profile.edit')->with('error', 'Failed to remove profile image. Please try again.');
        }
    }
}