<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\SubCategory;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\UserRequest;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            # code...
            $query = User::query();
            return DataTables::of($query)
            ->addColumn('action', function($item) {
                $buttons = '<a href="' .route('user.show', $item->id).'" class="btn btn-info btn-sm">Detail</a>';
                
                if ($item->roles == 'VALIDATOR' || $item->roles == 'KONTRIBUTOR') {
                    $buttons .= ' <a href="' .route('user.edit', $item->id).'" class="btn btn-warning btn-sm">Edit</a>';
                }

                return $buttons;
            })
            ->rawColumns(['action'])
            ->make();

        }
        return view('pages.admin.user.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         $subCategories = SubCategory::all();

        return view('pages.admin.user.create', compact('subCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $data = $request->all();
        $data['password'] = bcrypt($request->password);

        User::create($data);

        return redirect()->route('user.index');

    }

    /**
     * Display the specified resource.
     */
        public function show($id)
        {
            $user = User::with('userMajor.major')->findOrFail($id);
            return view('pages.admin.user.show', compact('user'));
        }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = User::findOrFail($id);

        $subCategories = SubCategory::all();

        return view('pages.admin.user.edit', compact('item', 'subCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, string $id)
    {
        $data = $request->all();

       if ($request->password) {
        $data['password'] = bcrypt($request->password);
       }
       else{
        unset($data['password']);
       }
       
        $item = User::findOrFail($id);

        $item->update($data);
        return redirect()->route('user.index');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = User::findOrFail($id);
        $item->delete();

        return redirect()->route('user.index');
    }
}
