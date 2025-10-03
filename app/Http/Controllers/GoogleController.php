<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
     public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

  public function handleGoogleCallback(Request $request)
{
    $googleUser = Socialite::driver('google')->stateless()->user();

    // Cari user berdasarkan email
    $user = User::firstOrCreate(
        ['email' => $googleUser->getEmail()],
        [
            'name' => $googleUser->getName(),
            'google_id' => $googleUser->getId(),
            'password' => bcrypt('password_google'), 
            'roles' => 'USER', // default role saat register
        ]
    );

    // Jika user sudah ada tapi belum punya google_id, isi
    if (!$user->google_id) {
        $user->google_id = $googleUser->getId();
        $user->save();
    }

    // Login user
    Auth::login($user);

    // Jika user baru, arahkan ke pengisian profil
    if ($user->wasRecentlyCreated) {
        return redirect()->route('profile.edit'); // ke /register-profile
    }

    // Redirect sesuai role
    switch ($user->roles) {
        case 'ADMIN':
            return redirect()->route('admin-dashboard');

        case 'KONTRIBUTOR':
            return redirect()->route('kontributor-dashboard');

        case 'USER':
            return redirect()->intended(route('home'));

        default:
            return redirect()->route('home');
    }
}
}
