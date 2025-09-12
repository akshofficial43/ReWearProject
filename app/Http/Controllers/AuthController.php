<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Handle OTP verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'otp' => ['required', 'digits:6'],
            'email' => ['required', 'email'],
        ]);

        $email = $validated['email'];
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('register')
                ->with('otp_required', true)
                ->with('email', $email)
                ->with('error', 'We could not find that user. Please register again.');
        }

        if ((string)$user->otp === (string)$validated['otp']) {
            $user->otp_verified = true;
            $user->otp = null;
            $user->save();

            Auth::login($user);
            return redirect()->route('home')->with('success', 'OTP verified! Registration complete.');
        }

        return redirect()->route('register')
            ->with('otp_required', true)
            ->with('email', $email)
            ->withErrors(['otp' => 'Invalid OTP. Please try again.']);
    }
    /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', 'min:8'],
            'terms' => ['required', 'accepted'],
            'profile_image' => ['nullable', 'image', 'max:1024'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Handle profile image upload if present
        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            $profileImagePath = $request->file('profile_image')->store('profile-images', 'public');
        }

        // Store address data as JSON
        $addressData = [
            'phone' => $request->phone,
        ];

        // Generate OTP
        $otp = rand(100000, 999999);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => json_encode($addressData),
            'role' => 'user', // Default role
            'profile_image' => $profileImagePath,
            'otp' => $otp,
            'otp_verified' => false,
        ]);

        // Send OTP email
        try {
            \Mail::raw('Your OTP code is: ' . $otp, function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Your ReWear OTP Code');
            });
        } catch (\Exception $e) {
            // Optionally handle mail errors
        }

        // Redirect to OTP verification form
        return redirect()->route('register')->with('otp_required', true)->with('email', $user->email);
    }

    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log the user out.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}