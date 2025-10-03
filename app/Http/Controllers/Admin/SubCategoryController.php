<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubCategoryRequest;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\CategoryRequest;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            # code...
            $query = SubCategory::with(['category']);
            return DataTables::of($query)
            ->addColumn('action', function($item) {
                return '<div class="btn-group">
                <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle mr-1 mb-1"
                Type="Button" id="action" data-toggle="dropdown">
                    Aksi
                </button>
                <div class="dropdown-menu">
                    <a href="' .route('subcategory.edit', $item->id).'" class="dropdown-item">Sunting</a>
                    <form action="' .route('subcategory.destroy', $item->id).'" method="POST">
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
        return view('pages.admin.subcategory.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('pages.admin.subcategory.create', [
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SubCategoryRequest $request)
    {
        $data = $request->all();

        $data['slug'] = Str::slug($request->name);
        if ($request->hasFile('photo')) {
        $file = $request->file('photo');
        $filename = time() . '_' . $file->getClientOriginalName();
        $data['photo'] = $file->storeAs('assets/subcategory', $filename, 'public');
    }
        SubCategory::create($data);

        return redirect()->route('subcategory.index');

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
        $item = SubCategory::findOrFail($id);
        $categories = Category::all();

        return view('pages.admin.subcategory.edit', [
            'item' => $item,
            'categories' => $categories
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, string $id)
    {
        $data = $request->all();

        $data['slug'] = Str::slug($request->name);
        $item = SubCategory::findOrFail($id);
        
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            // Buat nama file dengan menambahkan timestamp agar unik
            $filename = time() . '_' . $file->getClientOriginalName();
    
            // Hapus foto lama jika ada dan file tersebut ada di storage
            if ($item->photo && Storage::disk('public')->exists($item->photo)) {
                Storage::disk('public')->delete($item->photo);
            }
    
            $data['photo'] = $file->storeAs('assets/subcategory', $filename, 'public');
        } else {
            // Jika tidak ada foto baru, jangan ubah field photo
            $data['photo'] = $item->photo;
        }

        $item->update($data);
        return redirect()->route('subcategory.index');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = SubCategory::findOrFail($id);
        $item->delete();

        return redirect()->route('subcategory.index');
    }
}
