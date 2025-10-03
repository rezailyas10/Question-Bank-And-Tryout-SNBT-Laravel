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

class ExamController extends Controller
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

    // Tentukan query dan view berdasarkan role
    if ($user->roles === 'ADMIN') {
        $query = Exam::where('exam_type', 'latihan soal');
        $view  = 'pages.admin.exam.index';
    } elseif ($user->roles === 'KONTRIBUTOR' && $user->is_validator == 0) {
    // Kontributor biasa -> hanya exam yang dia buat
    $query = Exam::where('exam_type', 'latihan soal')
                 ->where('user_id', $user->id);
    $view  = 'pages.kontributor.exam.index';

} elseif ($user->roles === 'KONTRIBUTOR' && $user->is_validator == 1) {
    // Kontributor validator -> lihat exam berdasarkan sub_category_id
    $query = Exam::where('exam_type', 'latihan soal')
                 ->where('sub_category_id', $user->sub_category_id)->orWhere('user_id', $user->id);
    $view  = 'pages.kontributor.exam.index';
}
    elseif ($user->roles === 'VALIDATOR') {
        $query = Exam::where('exam_type', 'latihan soal') ->where('sub_category_id', auth()->user()->sub_category_id)->orWhere('user_id', $user->id);
        $view  = 'pages.validator.exam.index';
    }

    // Filter berdasarkan sub_category_id jika ada
    if ($request->filled('sub_category_id')) {
        $query->where('sub_category_id', $request->sub_category_id);
    }
    if ($request->filled('created_by')) {
    $query->where('created_by', $request->created_by);
}

    // Filter berdasarkan pencarian title atau created_by
    // Filter berdasarkan sub_category_id jika ada
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

    // Pagination dan query string tetap disertakan
    $exams = $query->latest()->paginate(10)->withQueryString();

    return view($view, [
        'sub_categories' => $subcategories,
        'exams' => $exams,
        'users' => $users
    ]);
}


    /**
     * Show the form for creating a new resource.
     */
   public function create()
    {
        $users = User::all();
         $user = Auth::user();
        $subcategories = SubCategory::all();
         if ($user->roles === 'ADMIN') {
        $view = 'pages.admin.exam.create';
        } elseif ($user->roles === 'KONTRIBUTOR') {
            $view = 'pages.kontributor.exam.create';
        }
        elseif ($user->roles === 'VALIDATOR') {
            $view = 'pages.validator.exam.create';
        }
        return view($view, [
            'users'          => $users,
            'sub_categories' => $subcategories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ExamRequest $request)
    {
      
        $data = $request->all();
        $data['slug'] = Str::slug($request->title);
        $data['created_by'] = auth()->user()->name;
        $data['user_id'] = auth()->user()->id;
        $data['exam_type'] = 'latihan soal';
        $data['tanggal_dibuka']  = $request->tanggal_dibuka;
        $data['tanggal_ditutup'] = $request->tanggal_ditutup;
        

        Exam::create($data);

        return redirect()->route('exam.index')->with('success', 'Bank soal berhasil dibuat!');

    }

    // public function store(ExamRequest $request)
    // {
      
    //     $data = $request->all();
    //     $data['slug'] = Str::slug($request->title);
    //     $data['created_by'] = auth()->user()->name;
    //     $data['sub_category_id'] = null;
    //     $data['tanggal_dibuka']  = null;
    //     $data['tanggal_ditutup'] = null;
    //     $data['exam_type'] = 'latihan soal';
        

    //     $exam = Exam::create($data);
    //         foreach ($request->subcategory_id as $i => $subcat) {
    //             TryoutSubtest::create([
    //                 'exam_id'         => $exam->id,
    //                 'subcategory_id' => $subcat,
    //                 'timer'           => 0,
    //             ]);
    //         }

    //     return redirect()->route('exam.index')->with('success', 'Bank soal berhasil dibuat!');

    // }

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
        
    if ($user->roles === 'ADMIN') {
        $exam = Exam::with([
        'questions' => function($query) {
            $query->orderBy('created_at', 'desc');
        },
        'questions.subCategory',
        'questions.user',
        'subCategory'
    ])->where('slug', $id)->firstOrFail();
        $view = 'pages.admin.exam.show';
    } 
    elseif ($user->roles === 'KONTRIBUTOR' && $user->is_validator == 1) {
    // Kontributor Validator -> bisa lihat semua pertanyaan
    $exam = Exam::with([
        'questions' => function($query) {
            $query->orderBy('created_at', 'desc');
        },
        'questions.subCategory',
        'questions.user',
        'subCategory'
    ])->where('slug', $id)->firstOrFail();

    $view = 'pages.kontributor.exam.show';

} elseif ($user->roles === 'KONTRIBUTOR' && $user->is_validator == 0) {
    // Kontributor biasa -> hanya bisa lihat pertanyaan dia sendiri
    $exam = Exam::with([
        'questions' => function($query) {
            $query->where('user_id', auth()->id())
                  ->orderBy('created_at', 'desc');
        },
        'questions.subCategory',
        'questions.user',
        'subCategory'
    ])->where('slug', $id)->firstOrFail();

    $view = 'pages.kontributor.exam.show';
}
elseif ($user->roles === 'VALIDATOR') {
    $exam = Exam::with([
        'questions' => function($query) {
            $query->orderBy('created_at', 'desc');
        },
        'questions.subCategory',
        'questions.user',
        'subCategory'
    ])->where('slug', $id)->firstOrFail();

    $view = 'pages.validator.exam.show';
}


 // Filter pencarian
    if ($search) {
        $questionsQuery->where('question_text', 'like', '%' . $search . '%');
    }

    // Urutkan dan load relasi tambahan

    $questions = $exam->questions()->orderBy('created_at', 'desc')->get();

return view($view, compact('exam', 'questions'));
    }
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $exam = Exam::findOrFail($id);
        $users = User::all();
        $subcategories = SubCategory::all();
        $user = Auth::user();
 if ($user->roles === 'ADMIN') {
        $view = 'pages.admin.exam.edit';
        } elseif ($user->roles === 'KONTRIBUTOR') {
            $view = 'pages.kontributor.exam.edit';
        }
        elseif ($user->roles === 'VALIDATOR') {
            $view = 'pages.validator.exam.edit';
        }

       return view($view, [
        'exam' => $exam,
        'users' => $users,
        'subcategories' => $subcategories,
    ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ExamRequest $request, $id)
{
    $exam = Exam::findOrFail($id);
    $data = $request->all();
    $data['user_id'] = auth()->user()->id;
    $data['slug'] = Str::slug($request->title);
     $data['tanggal_dibuka']  = null;
    $data['tanggal_ditutup'] = null;
    $data['exam_type'] = 'latihan soal';

    $exam->update($data);

    return redirect()
        ->back()
        ->with('success', 'Bank soal berhasil diperbarui!');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $exam = Exam::findOrFail($id);
        $exam->delete();
        return redirect()->route('exam.index')->with('success', 'Bank soal berhasil dihapus!');
    }
}
