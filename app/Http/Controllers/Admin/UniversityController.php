<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\UniversityRequest;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class UniversityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            # code...
            $query = university::query();
            return DataTables::of($query)
            ->addColumn('action', function($item) {
                return '<div class="btn-group">
                <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle mr-1 mb-1"
                Type="Button" id="action" data-toggle="dropdown">
                    Aksi
                </button>
                <div class="dropdown-menu">
                    <a href="' .route('university.edit', $item->id).'" class="dropdown-item">Sunting</a>
                    <form action="' .route('university.destroy', $item->id).'" method="POST">
                        '.method_field('delete') . csrf_field() .'
                        <button type="submit" class="dropdown-item text-danger">Hapus</button>
                    </form>
                </div>
                </div>
                </div> ';

            })
            ->editColumn('photo', function($item) {
                return $item->photo ?'<img src="'.Storage::url($item->photo).'" style="max-height: 48px;"/>' : '';
            })
            ->rawColumns(['action','photo'])
            ->make();

        }
        return view('pages.admin.university.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.admin.university.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(universityRequest $request)
    {
        $data = $request->all();

        $data['slug'] = Str::slug($request->name);
        if ($request->hasFile('photo')) {
        $file = $request->file('photo');
        $filename = time() . '_' . $file->getClientOriginalName();
        $data['photo'] = $file->storeAs('assets/university', $filename, 'public');
    }
        university::create($data);

        return redirect()->route('university.index');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = university::findOrFail($id);

        return view('pages.admin.university.edit', [
            'item' => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(universityRequest $request, string $id)
    {
        $data = $request->all();

        $data['slug'] = Str::slug($request->name);
        $item = university::findOrFail($id);
        
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            // Buat nama file dengan menambahkan timestamp agar unik
            $filename = time() . '_' . $file->getClientOriginalName();
    
            // Hapus foto lama jika ada dan file tersebut ada di storage
            if ($item->photo && Storage::disk('public')->exists($item->photo)) {
                Storage::disk('public')->delete($item->photo);
            }
    
            $data['photo'] = $file->storeAs('assets/university', $filename, 'public');
        } else {
            // Jika tidak ada foto baru, jangan ubah field photo
            $data['photo'] = $item->photo;
        }

        $item->update($data);
        return redirect()->route('university.index');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = university::findOrFail($id);
        if ($item->photo && Storage::disk('public')->exists($item->photo)) {
            Storage::disk('public')->delete($item->photo);
        }
        $item->delete();

        return redirect()->route('university.index');
    }
}
