<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\ProductRequest;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class BanksoalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            # code...
            $query = Product::with(['user','category']);
            return DataTables::of($query)
            ->addColumn('action', function($item) {
                return '<div class="btn-group">
                <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle mr-1 mb-1"
                Type="Button" id="action" data-toggle="dropdown">
                    Aksi
                </button>
                <div class="dropdown-menu">
                    <form action="' .route('product.destroy', $item->id).'" method="POST">
                        '.method_field('delete') . csrf_field() .'
                        <button type="submit" class="dropdown-item text-danger">Hapus</button>
                    </form>
                </div>
                </div>
                </div> ';

            })
            ->rawColumns(['action'])
            ->make();

        }
        return view('pages.admin.product.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        $categories = Category::all();
        return view('pages.admin.product.create', [
            'users' => $users,
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        Product::create($data);

        return redirect()->route('product.index');

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
        $item = Product::findOrFail($id);
        $users = User::all();
        $categories = Category::all();

        return view('pages.admin.product.edit', [
            'item' => $item,
            'users' => $users,
            'categories' => $categories
            
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, $id)
    {
        $data = $request->all();

       
        $item = Product::findOrFail($id);

        $item->update($data);
        $data['slug'] = Str::slug($request->name);
        return redirect()->route('product.index');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Product::findOrFail($id);
        $item->delete();

        return redirect()->route('product.index');
    }
}
