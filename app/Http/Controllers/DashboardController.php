<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Result;
use Illuminate\Http\Request;
use App\Models\ResultsEvaluation;
use App\Models\TransactionDetail;
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
    $customer = User::count();
    $user = Auth::user();

    // Initialize variables with default values
    $total_attempts = 0;
    $attempts_this_week = 0;
    $latihan_soal_attempts = 0;
    $tryout_attempts = 0;
    $latihan_soal_this_week = 0;
    $tryout_this_week = 0;
    $avg_latihan_soal_score = 0;
    $avg_tryout_score = 0;
    $highest_latihan_soal_score = 0;
    $highest_tryout_score = 0;
    $total_correct = 0;
    $total_wrong = 0;
    $total_empty = 0;
    $accuracy_rate = 0;
    $recent_evaluations = collect();
    $weekly_progress = [];

    // Tentukan view berdasarkan role
    if ($user->roles === 'ADMIN') {
        $view = 'pages.admin.dashboard';
    } elseif ($user->roles === 'KONTRIBUTOR') {
        $view = 'pages.kontributor.dashboard';
    } elseif ($user->roles === 'USER') {
        // Hitung total pengerjaan user
        $total_attempts = Result::where('user_id', $user->id)->count();

        // Hitung pengerjaan per minggu (7 hari terakhir)
        $attempts_this_week = Result::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();

        // Hitung latihan soal dan tryout berdasarkan exam_type
        $latihan_soal_attempts = Result::where('user_id', $user->id)
            ->whereHas('exam', function($query) {
                $query->where('exam_type', 'latihan soal');
            })
            ->count();
            
        $tryout_attempts = Result::where('user_id', $user->id)
            ->whereHas('exam', function($query) {
                $query->where('exam_type', 'tryout');
            })
            ->count();

        // Hitung latihan soal dan tryout minggu ini
        $latihan_soal_this_week = Result::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->whereHas('exam', function($query) {
                $query->where('exam_type', 'latihan soal');
            })
            ->count();
            
        $tryout_this_week = Result::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->whereHas('exam', function($query) {
                $query->where('exam_type', 'tryout');
            })
            ->count();

        // Hitung nilai rata-rata dari ResultsEvaluations
        $avg_latihan_soal_score = ResultsEvaluation::whereHas('result', function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->whereHas('exam', function($subQuery) {
                          $subQuery->where('exam_type', 'latihan soal');
                      });
            })
            ->avg('score') ?? 0;
            
        $avg_tryout_score = ResultsEvaluation::whereHas('result', function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->whereHas('exam', function($subQuery) {
                          $subQuery->where('exam_type', 'tryout');
                      });
            })
            ->avg('score') ?? 0;

        // Nilai tertinggi
        $highest_latihan_soal_score = ResultsEvaluation::whereHas('result', function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->whereHas('exam', function($subQuery) {
                          $subQuery->where('exam_type', 'latihan soal');
                      });
            })
            ->max('score') ?? 0;
            
        $highest_tryout_score = ResultsEvaluation::whereHas('result', function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->whereHas('exam', function($subQuery) {
                          $subQuery->where('exam_type', 'tryout');
                      });
            })
            ->max('score') ?? 0;

        // Statistik jawaban benar/salah/kosong total
        $total_correct = ResultsEvaluation::whereHas('result', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->sum('correct') ?? 0;
            
        $total_wrong = ResultsEvaluation::whereHas('result', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->sum('wrong') ?? 0;
            
        $total_empty = ResultsEvaluation::whereHas('result', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->sum('empty') ?? 0;

        // Hitung akurasi (persentase jawaban benar)
        $total_questions_answered = $total_correct + $total_wrong + $total_empty;
        $accuracy_rate = $total_questions_answered > 0 ? 
            round(($total_correct / $total_questions_answered) * 100, 1) : 0;

        // History terbaru dari ResultsEvaluations dengan relasi ke exam
        $recent_evaluations = ResultsEvaluation::whereHas('result', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['result.exam', 'result.user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Data untuk chart progress mingguan (4 minggu terakhir)
        $weekly_progress = [];
        for ($i = 3; $i >= 0; $i--) {
            $start_date = Carbon::now()->subWeeks($i)->startOfWeek();
            $end_date = Carbon::now()->subWeeks($i)->endOfWeek();
            
            $week_attempts = Result::where('user_id', $user->id)
                ->whereBetween('created_at', [$start_date, $end_date])
                ->count();
                
            $week_avg_score = ResultsEvaluation::whereHas('result', function($query) use ($user, $start_date, $end_date) {
                    $query->where('user_id', $user->id)
                          ->whereBetween('created_at', [$start_date, $end_date]);
                })
                ->avg('score') ?? 0;
            
            $weekly_progress[] = [
                'week' => 'Minggu ' . (4 - $i),
                'attempts' => $week_attempts,
                'avg_score' => round($week_avg_score, 1)
            ];
        }
        
        $view = 'pages.dashboard.dashboard';
    }

    return view($view, compact(
        'total_attempts',
        'attempts_this_week',
        'latihan_soal_attempts',
        'tryout_attempts',
        'latihan_soal_this_week',
        'tryout_this_week',
        'avg_latihan_soal_score',
        'avg_tryout_score',
        'highest_latihan_soal_score',
        'highest_tryout_score',
        'total_correct',
        'total_wrong',
        'total_empty',
        'accuracy_rate',
        'recent_evaluations',
        'weekly_progress'
    ));
}
}
