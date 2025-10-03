<?php

namespace App\Http\Controllers\Admin;

use App\Models\Exam;
use App\Models\User;
use App\Models\Question;
use App\Models\SubCategory;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TryoutSubtest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\ExamRequest;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\Admin\ProductRequest;

class TryoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */

public function index(Request $request)
{
    $subcategories = SubCategory::all();
   $users = User::whereHas('exams', function ($q) {
    $q->where('exam_type', 'tryout');
    })->get();
    $user = Auth::user();

    // Tentukan view berdasarkan role (JANGAN DIUBAH)
    if ($user->roles === 'ADMIN') {
        $query = Exam::where('exam_type', 'tryout');
                    //  ->where('created_by', $user->name);
        $view = 'pages.admin.tryout.index';
    } elseif ($user->roles == 'KONTRIBUTOR') {
        $query = Exam::where('exam_type', 'tryout');
        $view = 'pages.kontributor.tryout.index';
    }
    elseif ($user->roles == 'VALIDATOR') {
        $query = Exam::where('exam_type', 'tryout');
        $view = 'pages.validator.tryout.index';
    }

    // Tambahkan filter berdasarkan sub_category_id jika ada
    if ($request->filled('sub_category_id')) {
        $query->where('sub_category_id', $request->sub_category_id);
    }

  if ($request->filled('user_id')) {
    $query->where('user_id', $request->user_id);
}

    // Tambahkan filter pencarian (title atau nama user)
if ($request->filled('search')) {
    $query->where(function ($q) use ($request) {
        $q->where('title', 'like', '%' . $request->search . '%')
          ->orWhereHas('user', function ($q2) use ($request) {
              $q2->where('name', 'like', '%' . $request->search . '%');
          });
    });
}


    // Pagination dan withQueryString agar filter tetap saat pindah halaman
    $exams = $query->latest()->paginate(10)->withQueryString();

    return view($view, [
        'sub_categories' => $subcategories,
        'users' => $users,
        'exams' => $exams,
    ]);
}


    /**
     * Show the form for creating a new resource.
     */
   public function create()
    {
        $users = User::all();
        $subcategories = SubCategory::all();
        return view('pages.admin.tryout.create', [
            'users'          => $users,
            'sub_categories' => $subcategories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(ExamRequest $request)
    // {
      
    //     $data = $request->all();
    //     $data['slug'] = Str::slug($request->title);
    //     $data['created_by'] = auth()->user()->name;

    //     if ($data['exam_type'] === 'tryout') {
    //         $data['tanggal_dibuka']  = $request->tanggal_dibuka;
    //         $data['tanggal_ditutup'] = $request->tanggal_ditutup;
    //     }

    //     $exam = Exam::create($data);
    //     if ($data['exam_type'] === 'tryout') {
    //         foreach ($request->subcategory_id as $i => $subcat) {
    //             TryoutSubtest::create([
    //                 'exam_id'         => $exam->id,
    //                 'subcategory_id' => $subcat,
    //                 'timer'           => $request->timer[$i],
    //             ]);
    //         }
    //     }

    //     return redirect()->route('exam.index')->with('success', 'Bank soal berhasil dibuat!');

    // }

    public function store(ExamRequest $request)
    {
      
        $data = $request->all();
        $data['slug'] = Str::slug($request->title);
        $data['created_by'] = auth()->user()->name;
        $data['user_id'] = auth()->user()->id;
        $data['sub_category_id'] = null;
        $data['exam_type'] = 'tryout';
         $data['tanggal_dibuka']  = $request->tanggal_dibuka;
         $data['tanggal_ditutup'] = $request->tanggal_ditutup;
    

        Exam::create($data);

        return redirect()->route('tryout.index')->with('success', 'Bank soal berhasil dibuat!');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();
         $search = request()->input('search');

    $exam = Exam::with('subCategory')->where('slug', $id)->firstOrFail();

    // Base query questions
    $questionsQuery = Question::where('exam_id', $exam->id);
        if ($user->roles == 'ADMIN') {
        $exam = Exam::with([
        'questions' => function($query) {
            $query->orderBy('created_at', 'desc');
        },
        'questions.subCategory',
        'questions.user',
        'subCategory'
    ])->where('slug', $id)->firstOrFail();
        $view = 'pages.admin.tryout.show';
    } 
    elseif ($user->roles === 'KONTRIBUTOR' && $user->is_validator == 0) {
    $exam = Exam::with([
        'questions' => function($query) {
            $query->where('user_id', auth()->id())
                  ->orderBy('created_at', 'desc');
        },
        'questions.subCategory',
        'questions.user',
        'subCategory'
    ])->where('slug', $id)->firstOrFail();
    
    $view = 'pages.kontributor.tryout.show';
}
elseif ($user->roles === 'KONTRIBUTOR' && $user->is_validator == 1) {
    $exam = Exam::with([
        'questions' => function($query) use ($user) {
            $query->where('sub_category_id', $user->sub_category_id)->orWhere('user_id', $user->id) // filter sesuai sub_category_id validator
                  ->orderBy('created_at', 'desc');
        },
        'questions.subCategory',
        'questions.user',
        'subCategory'
    ])->where('slug', $id)->firstOrFail();

    $view = 'pages.kontributor.tryout.show';
}
elseif ($user->roles == 'VALIDATOR') {
    $exam = Exam::with([
        'questions' => function($query) use ($user) {
            $query->where('sub_category_id', $user->sub_category_id) // filter sesuai sub_category_id validator
                  ->orderBy('created_at', 'desc');
        },
        'questions.subCategory',
        'questions.user',
        'subCategory'
    ])->where('slug', $id)->firstOrFail();

    $view = 'pages.validator.tryout.show';
}
// Filter pencarian
    if ($search) {
        $questionsQuery->where('question_text', 'like', '%' . $search . '%');
    }

    // Urutkan dan load relasi tambahan
    $questions = $questionsQuery->with(['subCategory', 'user'])->orderBy('created_at', 'desc')->get();

    // Tempelkan hasil pertanyaan ke relasi exam
    $exam->setRelation('questions', $questions);
        return view($view, compact('exam'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $exam = Exam::findOrFail($id);
        $users = User::all();
        $subcategories = SubCategory::all();

        return view('pages.admin.tryout.edit', [
            'exam'           => $exam,
            'users'          => $users,
            'sub_categories' => $subcategories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ExamRequest $request, $id)
{
    $exam = Exam::findOrFail($id);
    $data = $request->all();
    $data['slug'] = Str::slug($request->title);
     $data['exam_type'] = 'tryout';
     $data['user_id'] = auth()->user()->id;
    $data['sub_category_id'] = null;
    $data['tanggal_dibuka']  = $request->tanggal_dibuka;
    $data['tanggal_ditutup'] = $request->tanggal_ditutup;


    $exam->update($data);

    return redirect()
        ->back()
        ->with('success', 'Tryout berhasil diperbarui!');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $exam = Exam::findOrFail($id);
        $exam->delete();
        return redirect()->route('tryout.index')->with('success', 'Tryout berhasil dihapus!');
    }
}
