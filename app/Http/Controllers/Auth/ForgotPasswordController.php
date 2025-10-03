<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

      public function showForm()
    {
        return view('auth.passwords.email');
    }

    public function sendLink(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
    ]);

    try {
        $token = Str::random(64);
        $email = strtolower($request->email);

        // Update or insert pakai model
        PasswordReset::updateOrCreate(
            ['email' => $email],
            ['token' => $token, 'created_at' => Carbon::now()]
        );

        $link = url("/reset-password/{$token}?email={$email}");

        Mail::raw("Klik link berikut untuk reset password:\n\n$link", function ($message) use ($email) {
            $message->to($email)->subject('Reset Password - Aplikasi Kamu');
        });

        Log::info('Reset password link berhasil dikirim ke: ' . $email);

        return back()->with('status', 'Link reset password sudah dikirim ke email Anda. Silakan cek kotak masuk atau folder spam jika tidak menemukan notifikasi.');
    } catch (\Exception $e) {
        Log::error('Gagal mengirim link reset password: ' . $e->getMessage());
        return back()->withErrors(['email' => 'Terjadi kesalahan. Silakan coba lagi.']);
    }
}
}
