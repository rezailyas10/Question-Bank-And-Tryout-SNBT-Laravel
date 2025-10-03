<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardSettingController extends Controller
{
    public function store()
    {
        $user = Auth::user();
        $categories = Category::all();
        $province = Province::all();
        $regency = Regency::all();
        $district = District::all();
        $village = Village::all();
        return view('pages.dashboard.dashboard-settings', [
            'user' => $user,
            'categories' => $categories,
            'province' => $province,
            'regency' => $regency,
            'district' => $district,
            'village' => $village,
        ]);
    }

    public function account()
{
    $user = Auth::user();
    
    // Tentukan view berdasarkan role
    if ($user->roles === 'ADMIN') {
        $view = 'pages.admin.dashboard-account';
    } elseif ($user->roles === 'KONTRIBUTOR') {
        $view = 'pages.kontributor.dashboard-account';
    } elseif(($user->roles === 'USER')) {
        $view = 'pages.dashboard.dashboard-account';
    } elseif(($user->roles === 'SALES')) {
        $view = 'pages.sales.dashboard-account';
    }elseif(($user->roles === 'VALIDATOR')) {
        $view = 'pages.validator.dashboard-account';

    return view($view, [
        'user' => $user
    ]);
}

    public function update(Request $request, $redirect)
{
    try {
        $data = $request->all();

        $item = Auth::user();
        $item->update($data);

        return redirect()->route($redirect)->with('success', 'Data berhasil diperbarui!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

public function updatePassword(Request $request)
{
    try {
        // Jika ada password, berarti ini update password
        if ($request->has('password') && $request->filled('password')) {
            $request->validate([
                'current_password' => 'required',
                'password' => 'required|min:8|confirmed',
            ]);

            $user = Auth::user();
            
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->with('error', 'Password lama tidak sesuai!');
            }
            
            $user->update([
                'password' => Hash::make($request->password)
            ]);
        } else {
            // Update data lain
            $data = $request->all();
            $item = Auth::user();
            $item->update($data);
        }

        return redirect()->back()->with('success', 'Data berhasil diperbarui!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}


public function checkPassword(Request $request)
{
    $user = auth()->user(); // Pastikan user sudah login

    if (!$user) {
        return response()->json(['valid' => false, 'message' => 'Not authenticated']);
    }

    $isValid = Hash::check($request->current_password, $user->password);

    return response()->json([
        'valid' => $isValid
    ]);
}
}
