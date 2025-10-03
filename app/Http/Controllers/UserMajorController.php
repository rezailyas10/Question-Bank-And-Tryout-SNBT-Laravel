<?php

namespace App\Http\Controllers;

use App\Models\Major;
use App\Models\University;
use App\Models\UserMajor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserMajorController extends Controller
{
     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
       public function index(Request $request)
{
    $universities = University::all();
    $majors = Major::all();
     $userMajors = Auth::user()->userMajor()->pluck('major_id')->toArray();

    return view('pages.dashboard.user-major', compact('universities', 'majors', 'universities','userMajors'));

}

public function getMajorsByUniversity($id)
{
    $majors = Major::where('university_id', $id)->get();
    return response()->json($majors);
}

 public function store(Request $request)
{
    $data = $request->validate([
        'majors'   => 'required|array|min:1',
        'majors.*' => 'integer|exists:majors,id',
    ]);

    $submitted = array_unique($data['majors']); // hilangkan duplikat
    $existing = Auth::user()->userMajor()->pluck('major_id')->toArray();

    $toCreate = array_diff($submitted, $existing);
    $toUpdate = array_intersect($submitted, $existing);
    $toDelete = array_diff($existing, $submitted);

    foreach ($toCreate as $mid) {
        UserMajor::create([
            'user_id'  => Auth::id(),
            'major_id' => $mid,
        ]);
    }

    // 2. Update data yang sudah ada
    foreach ($toUpdate as $mid) {
        UserMajor::where('user_id', Auth::id())
                 ->where('major_id', $mid)
                 ->update([
                     'major_id' => $mid, // ganti/isi kolom lain jika ada
                 ]);
    }

    if (!empty($toDelete)) {
        UserMajor::where('user_id', Auth::id())
                 ->whereIn('major_id', $toDelete)
                 ->delete();
    }

    return back()->with('success', 'Pilihan jurusan berhasil disimpan.');
}



}
