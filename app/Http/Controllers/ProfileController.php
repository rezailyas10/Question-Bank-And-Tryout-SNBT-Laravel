<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
      public function edit()
{
    $user = Auth::user();

    return view('auth.profile', [
        'user' => $user
    ]);
}

    public function update(Request $request)
{
    try {
        $data = $request->all();

        $item = Auth::user();
        $item->update($data);

        return redirect()->route('home')->with('success', 'Data berhasil diperbarui!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
}
