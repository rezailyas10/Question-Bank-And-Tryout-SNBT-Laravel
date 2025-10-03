<?php

namespace App\Http\Controllers\Admin;

use App\Models\Blog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\blogRequest;
use Yajra\DataTables\Facades\DataTables;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            # code...
             $query = Blog::where('author', Auth::user()->name); 
            return DataTables::of($query)
            ->addColumn('action', function($item) {
                return '<div class="btn-group">
                <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle mr-1 mb-1"
                Type="Button" id="action" data-toggle="dropdown">
                    Aksi
                </button>
                <div class="dropdown-menu">
                    <a href="' .route('blog.edit', $item->id).'" class="dropdown-item">Sunting</a>
                    <form action="' .route('blog.destroy', $item->id).'" method="POST">
                        '.method_field('delete') . csrf_field() .'
                        <button type="submit" class="dropdown-item text-danger">Hapus</button>
                    </form>
                </div>
                </div>
                </div> ';

            })
            ->editColumn('cover', function($item) {
                return $item->cover ?'<img src="'.Storage::url($item->cover).'" style="max-height: 48px;"/>' : '';
            })
            ->rawColumns(['action','cover'])
            ->make();

        }
        return view('pages.admin.blog.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.admin.blog.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $data['slug'] = Str::slug($request->title);
        $data['author'] = auth()->user()->name;
        if ($request->hasFile('cover')) {
        $file = $request->file('cover');
        $filename = time() . '_' . $file->getClientOriginalName();
        $data['cover'] = $file->storeAs('assets/blog', $filename, 'public');
    }
        blog::create($data);

        return redirect()->route('blog.index');

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
        $item = blog::findOrFail($id);

        return view('pages.admin.blog.edit', [
            'item' => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->all();

        $data['slug'] = Str::slug($request->title);
        $data['author'] = auth()->user()->name;
        $item = blog::findOrFail($id);
        
        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            // Buat nama file dengan menambahkan timestamp agar unik
            $filename = time() . '_' . $file->getClientOriginalName();
    
            // Hapus foto lama jika ada dan file tersebut ada di storage
            if ($item->cover && Storage::disk('public')->exists($item->cover)) {
                Storage::disk('public')->delete($item->cover);
            }
    
            $data['cover'] = $file->storeAs('assets/blog', $filename, 'public');
        } else {
            // Jika tidak ada foto baru, jangan ubah field cover
            $data['cover'] = $item->cover;
        }

        $item->update($data);
        return redirect()->route('blog.index');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = blog::findOrFail($id);
        if ($item->cover && Storage::disk('public')->exists($item->cover)) {
            Storage::disk('public')->delete($item->cover);
        }
        $item->delete();

        return redirect()->route('blog.index');
    }
}
