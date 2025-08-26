<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        // Try to authenticate
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Check if user is admin
            if ($user->role === 'admin') {
                $request->session()->regenerate();
                
                // Update last login
                $user->update(['last_login_at' => now()]);
                
                return redirect()->intended(route('admin.dashboard'));
            } else {
                Auth::logout();
                return back()->with('error', 'ليس لديك صلاحية للوصول لهذه الصفحة');
            }
        }

        return back()->with('error', 'البريد الإلكتروني أو كلمة المرور غير صحيحة');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }

    /**
     * Create admin user if doesn't exist (for development)
     */
    public function createAdminUser()
    {
        $admin = User::where('email', 'admin@suntop.com')->first();
        
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin User',
                'username' => 'admin',
                'full_name' => 'مدير النظام',
                'email' => 'admin@suntop.com',
                'phone' => '+20 100 000 0001',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء حساب الإدارة بنجاح',
                'admin' => $admin
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'حساب الإدارة موجود بالفعل',
            'admin' => $admin
        ]);
    }
}
