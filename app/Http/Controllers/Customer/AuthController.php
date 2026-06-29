<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CustomerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('customer.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            if (Auth::user()->role !== 'customer') {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun ini bukan customer.']);
            }
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Kredensial tidak valid.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        return view('customer.auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'nim' => ['nullable', 'string', 'max:50'],
            'faculty' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
        ]);

        CustomerProfile::create([
            'user_id' => $user->id,
            'phone'   => $validated['phone']   ?? null,
            'nim'     => $validated['nim']     ?? null,
            'faculty' => $validated['faculty'] ?? null,
        ]);

        Auth::login($user);

        return redirect('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/customer/login');
    }

    public function profile()
    {
        $user = Auth::user();
        $user->load('customerProfile');
        return view('customer.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'nim' => ['nullable', 'string', 'max:50'],
            'faculty' => ['nullable', 'string', 'max:100'],
            'avatar' => ['nullable', 'image', 'max:2048'], // 2MB Max
        ]);

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->name = $validated['name'];
        $user->save();

        CustomerProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => $validated['phone'],
                'nim' => $validated['nim'],
                'faculty' => $validated['faculty'],
            ]
        );

        return redirect()->back()->with('success', 'Profil berhasil diperbarui!');
    }
}
