<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    public function showResetForm(Request $request, $token)
    {
        $email = $request->query('email');
        return view('auth.passwords.reset', compact('token', 'email'));
    }

    public function reset(Request $request)
{
    Log::info('Proses reset password dimulai', $request->all());

    $request->validate([
        'email' => 'required|email|exists:users,email',
        'password' => 'required|min:6|confirmed',
    ]);

    $email = strtolower($request->email);

    // Cek apakah email ada di tabel password_resets
    $reset = DB::table('password_resets')
        ->where('email', $email)
        ->first();


    // Reset password user
    $user = User::where('email', $email)->first();
    if (!$user) {
        Log::error('User tidak ditemukan dengan email: ' . $email);
        return back()->withErrors(['email' => 'User tidak ditemukan.']);
    }

    $user->password = Hash::make($request->password);
    $user->save();

    Log::info('Password berhasil diubah untuk user: ' . $email);

    // Hapus token
    DB::table('password_resets')->where('email', $email)->delete();
    Log::info('Token reset dihapus dari database');

    return redirect()->route('login')->with('status', 'Password berhasil direset.');
}

}
