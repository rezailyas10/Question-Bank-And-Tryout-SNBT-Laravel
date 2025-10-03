<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\CategoryRequest;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            # code...
            $query = Category::query();
            return DataTables::of($query)
            ->addColumn('action', function($item) {
                return '<div class="btn-group">
                <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle mr-1 mb-1"
                Type="Button" id="action" data-toggle="dropdown">
                    Aksi
                </button>
                <div class="dropdown-menu">
                    <a href="' .route('category.edit', $item->id).'" class="dropdown-item">Sunting</a>
                    <form action="' .route('category.destroy', $item->id).'" method="POST">
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
        return view('pages.admin.category.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        $data = $request->all();

        $data['slug'] = Str::slug($request->name);
        if ($request->hasFile('photo')) {
        $file = $request->file('photo');
        $filename = time() . '_' . $file->getClientOriginalName();
        $data['photo'] = $file->storeAs('assets/category', $filename, 'public');
    }
        Category::create($data);

        return redirect()->route('category.index');

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
        $item = Category::findOrFail($id);

        return view('pages.admin.category.edit', [
            'item' => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, string $id)
    {
        $data = $request->all();

        $data['slug'] = Str::slug($request->name);
        $item = Category::findOrFail($id);
        
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            // Buat nama file dengan menambahkan timestamp agar unik
            $filename = time() . '_' . $file->getClientOriginalName();
    
            // Hapus foto lama jika ada dan file tersebut ada di storage
            if ($item->photo && Storage::disk('public')->exists($item->photo)) {
                Storage::disk('public')->delete($item->photo);
            }
    
            $data['photo'] = $file->storeAs('assets/category', $filename, 'public');
        } else {
            // Jika tidak ada foto baru, jangan ubah field photo
            $data['photo'] = $item->photo;
        }

        $item->update($data);
        return redirect()->route('category.index');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Category::findOrFail($id);
        if ($item->photo && Storage::disk('public')->exists($item->photo)) {
            Storage::disk('public')->delete($item->photo);
        }
        $item->delete();

        return redirect()->route('category.index');
    }
}
