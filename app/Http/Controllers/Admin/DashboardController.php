<?php

namespace App\Http\Controllers\Admin;

use App\Models\Exam;
use App\Models\Result;
use App\Models\User;
use App\Models\RegistrationITI;
use App\Models\Question;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
         $user = Auth::user();
    if ($user->roles !== 'ADMIN') {
        return redirect()->back();
    }
    
    // Statistik dasar
    $total_users = User::count();
    $total_kontributor = User::where('roles', 'KONTRIBUTOR')->count();
    $total_questions = Question::count();
    $total_results = Result::count();
    
    // Statistik ujian berdasarkan tipe
    $total_latihan_soal = Exam::where('exam_type', 'latihan soal')->count();
    $total_tryout = Exam::where('exam_type', 'tryout')->count();
    
    // Pertanyaan yang perlu direview
    $pending_questions = Question::where('status', 'Ditinjau')->count();
    
    // Ujian yang dibuka hari ini
    $today_exams = Exam::whereDate('tanggal_dibuka', today())
        ->where('is_published', 1)
        ->count();
    
    // Aktivitas terbaru - Ujian yang baru dibuat (5 terbaru)
    $recent_exams = Exam::
        withCount('questions')
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
    
    // Aktivitas terbaru - Hasil ujian (5 terbaru)
    $recent_results = Result::with(['user', 'exam'])
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
    
    // Aktivitas terbaru - User baru (5 terbaru)
    $recent_users = User::orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
    
    return view('pages.admin.dashboard', compact(
        'total_users',
        'total_kontributor',
        'total_questions',
        'total_results',
        'total_latihan_soal',
        'total_tryout',
        'pending_questions',
        'today_exams',
        'recent_exams',
        'recent_results',
        'recent_users'
    ));
     
    }
   public function kontributor()
{
    $user = Auth::user();

    if ($user->roles !== 'KONTRIBUTOR') {
        return redirect()->back();
    }

    $user_id = Auth::id();
    
    // Hitung statistik pertanyaan berdasarkan user yang login (kontributor)
    $total_questions = Question::where('user_id', $user_id)->count();
    $accepted_questions = Question::where('user_id', $user_id)
        ->where('status', 'Diterima')
        ->count();
    $reviewed_questions = Question::where('user_id', $user_id)
        ->where('status', 'Ditinjau')
        ->count();
    $rejected_questions = Question::where('user_id', $user_id)
        ->where('status', 'Ditolak')
        ->count();
    // 2. Pertanyaan minggu ini
    $questions_this_week = Question::where('user_id', $user_id)
        ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
        ->count();

    // 3. Tingkat penerimaan (acceptance rate)
    $acceptance_rate = $total_questions > 0 ? round(($accepted_questions / $total_questions) * 100, 1) : 0;
    // Hitung soal berdasarkan exam_type untuk latihan soal dan tryout
    // Hanya soal yang statusnya "Diterima"
    $latihan_soal_count = Question::where('user_id', $user_id)
        // ->where('status', 'Diterima')
        ->whereHas('exam', function($query) {
            $query->where('exam_type', 'latihan soal');
        })
        ->count();
        
    $tryout_count = Question::where('user_id', $user_id)
        ->where('status', 'Diterima')
        ->whereHas('exam', function($query) {
            $query->where('exam_type', 'tryout');
        })
        ->count();
    
    $user = auth()->user();

// Base query untuk pertanyaan terbaru
if ($user->is_validator == 1) {
    // Jika validator → ambil berdasarkan sub_category_id
    $recent_questions = Question::where('sub_category_id', $user->sub_category_id)
        ->orderBy('updated_at', 'desc')
        ->limit(10)
        ->get();

    $recent_accepted_questions = Question::where('sub_category_id', $user->sub_category_id)
        ->where('status', 'Diterima')
        ->with('exam')
        ->orderBy('updated_at', 'desc')
        ->limit(10)
        ->get();

    $recent_reviewed_questions = Question::where('sub_category_id', $user->sub_category_id)
        ->where('status', 'Ditinjau')
        ->with('exam')
        ->orderBy('updated_at', 'desc')
        ->limit(10)
        ->get();
} else {
    // Jika bukan validator → ambil berdasarkan user_id
    $recent_questions = Question::where('user_id', $user->id)
        ->orderBy('updated_at', 'desc')
        ->limit(5)
        ->get();

    $recent_accepted_questions = Question::where('user_id', $user->id)
        ->where('status', 'Diterima')
        ->with('exam')
        ->orderBy('updated_at', 'desc')
        ->limit(10)
        ->get();

    $recent_reviewed_questions = Question::where('user_id', $user->id)
        ->where('status', 'Ditinjau')
        ->with('exam')
        ->orderBy('updated_at', 'desc')
        ->limit(10)
        ->get();
}

    // 1. Ambil 5 pertanyaan terbaru (semua status)
    
    // Ambil 5 ujian terbaru dari kontributor (opsional) - DISABLED
    // $recent_exams = Exam::where('created_by', Auth::user()->name)
    //     ->orWhere('created_by', Auth::id())
    //     ->withCount('questions') // Menghitung jumlah pertanyaan per ujian
    //     ->orderBy('created_at', 'desc')
    //     ->limit(5)
    //     ->get();
    
    return view('pages.kontributor.dashboard', compact(
        'total_questions',
        'accepted_questions',
        'reviewed_questions',
        'rejected_questions',
        'latihan_soal_count',
        'tryout_count',
        'recent_questions',
        'recent_accepted_questions',
        'recent_reviewed_questions',
        'questions_this_week',
        'acceptance_rate'
        // 'recent_exams' // DISABLED
    ));
}
   public function validator()
{
    $user = Auth::user();

    if ($user->roles !== 'VALIDATOR') {
        return redirect()->back();
    }

    $user_id = Auth::id();
    
    // Hitung statistik pertanyaan berdasarkan user yang login (kontributor)
    $total_questions = Question::count();
    $accepted_questions = Question::
        where('status', 'Diterima')
        ->count();
    $reviewed_questions = Question::
        where('status', 'Ditinjau')
        ->count();
    $rejected_questions = Question::
        where('status', 'Ditolak')
        ->count();
    // 2. Pertanyaan minggu ini
    $questions_this_week = Question::
        whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
        ->count();

    // 3. Tingkat penerimaan (acceptance rate)
    $acceptance_rate = $total_questions > 0 ? round(($accepted_questions / $total_questions) * 100, 1) : 0;
    // Hitung soal berdasarkan exam_type untuk latihan soal dan tryout
    // Hanya soal yang statusnya "Diterima"
    $latihan_soal_count = Question::
        // ->where('status', 'Diterima')
        whereHas('exam', function($query) {
            $query->where('exam_type', 'latihan soal');
        })
        ->count();
        
    $tryout_count = Question::
        where('status', 'Diterima')
        ->whereHas('exam', function($query) {
            $query->where('exam_type', 'tryout');
        })
        ->count();
    
    // // Ambil 5 pertanyaan terbaru dari kontributor (semua status) - berdasarkan updated_at
    $recent_questions = Question::
        orderBy('updated_at', 'desc')
        ->limit(5)
        ->get();
    
    // Ambil 5 pertanyaan terbaru yang sudah diterima dari kontributor - berdasarkan updated_at
    $recent_accepted_questions = Question::
        where('status', 'Diterima')
        ->with('exam') // Eager load exam untuk mendapatkan exam_type
        ->orderBy('updated_at', 'desc')
        ->limit(10)
        ->get();
    $recent_reviewed_questions = Question::
        where('status', 'Ditinjau')
        ->with('exam') // Eager load exam untuk mendapatkan exam_type
        ->orderBy('updated_at', 'desc')
        ->limit(10)
        ->get();

    // 1. Ambil 5 pertanyaan terbaru (semua status)
    
    // Ambil 5 ujian terbaru dari kontributor (opsional) - DISABLED
    // $recent_exams = Exam::where('created_by', Auth::user()->name)
    //     ->orWhere('created_by', Auth::id())
    //     ->withCount('questions') // Menghitung jumlah pertanyaan per ujian
    //     ->orderBy('created_at', 'desc')
    //     ->limit(5)
    //     ->get();
    
    return view('pages.validator.dashboard', compact(
        'total_questions',
        'accepted_questions',
        'reviewed_questions',
        'rejected_questions',
        'latihan_soal_count',
        'tryout_count',
        'recent_questions',
        'recent_accepted_questions',
        'recent_reviewed_questions',
        'questions_this_week',
        'acceptance_rate'
        // 'recent_exams' // DISABLED
    ));
}

 public function sales()
{
    $user = Auth::user();

    if ($user->roles !== 'SALES') {
        return redirect()->back();
    }

    $user_id = Auth::id();
    
    $totalRegistrations = RegistrationITI::count();
$contactedCount = RegistrationITI::where('status', 'sudah dihubungi')->count();
$notContactedCount = RegistrationITI::where('status', 'belum dihubungi')->count();

$registrations = RegistrationITI::with(['result.user', 'result.exam'])
    ->latest()
    ->take(5)
    ->get();

return view('pages.sales.dashboard', compact(
    'totalRegistrations',
    'contactedCount',
    'notContactedCount',
    'registrations'
));
}
    
}
