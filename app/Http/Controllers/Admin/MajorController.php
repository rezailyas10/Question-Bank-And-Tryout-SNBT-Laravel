<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MajorRequest;
use App\Models\University;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\universityRequest;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class MajorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
{
    $universities = University::all();

    $query = Major::with('university');

    if ($request->filled('university_id')) {
        $query->where('university_id', $request->university_id);
    }

    if ($request->filled('search')) {
        $query->where('name', 'like', '%'.$request->search.'%');
    }

   $majors = $query
    ->latest()
    ->simplePaginate(10)      // ← pastikan ini dipanggil di atas, TANPA ->get()
    ->withQueryString();

    return view('pages.admin.major.index', [
        'sub_universities' => $universities,
        'majors' => $majors,
    ]);
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $universities = university::all();
        return view('pages.admin.major.create', [
            'universities' => $universities
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $data['slug'] = Str::slug($request->name);
        if ($request->hasFile('photo')) {
        $file = $request->file('photo');
        $filename = time() . '_' . $file->getClientOriginalName();
        $data['photo'] = $file->storeAs('assets/major', $filename, 'public');
    }
        major::create($data);

        return redirect()->route('major.index');

    }

    /**
     * Display the specified resource.
     */
     public function show(string $id)
    {
        $major = major::with(['university'])->firstOrFail();
        return view('pages.admin.major.show', compact('major'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = major::findOrFail($id);
        $universities = university::all();

        return view('pages.admin.major.edit', [
            'item' => $item,
            'universities' => $universities
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->all();

        $data['slug'] = Str::slug($request->name);
        $item = major::findOrFail($id);
        $item->update($data);
        return redirect()->route('major.index');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = major::findOrFail($id);
        $item->delete();

        return redirect()->route('major.index');
    }
}
