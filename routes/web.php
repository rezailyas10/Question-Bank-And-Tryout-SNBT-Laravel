<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/about', [App\Http\Controllers\HomeController::class, 'about'])->name('about');

Route::get('/auth/google', [App\Http\Controllers\GoogleController::class, 'redirectToGoogle'])->name('redirectToGoogle');
Route::get('/auth/google/callback', [App\Http\Controllers\GoogleController::class, 'handleGoogleCallback']);

Route::get('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showForm'])->name('password.request');
Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendLink'])->name('password.email');

Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
Route::get('/categories', [App\Http\Controllers\CategoryController::class, 'index'])->name('categories');
Route::get('/banksoal', [App\Http\Controllers\BanksoalController::class, 'index'])->name('bank-soal');
Route::get('/banksoal/{id}', [App\Http\Controllers\BanksoalController::class, 'detail'])->name('bank-soal-detail');
Route::get('/blog', [App\Http\Controllers\BlogController::class, 'index'])->name('blog');
    Route::get('/blog/{slug}', [App\Http\Controllers\BlogController::class, 'show'])->name('blog-show');

Route::get('/get-tryout', [App\Http\Controllers\TryoutController::class, 'index'])->name('tryout');
Route::get('/get-tryout1', [App\Http\Controllers\TryoutController::class, 'test'])->name('tryout1');

     
Route::get('/categories/{id}', [App\Http\Controllers\CategoryController::class, 'detail'])->name('categories-detail');
Route::get('/get-exam/{id}', [App\Http\Controllers\ExamController::class, 'detail'])->name('exam');
Route::post('/upload-image', [App\Http\Controllers\ImageUploadController::class, 'upload']);
// routes/web.php atau routes/api.php (tergantung kamu pakai yang mana)
Route::post('/delete-image', [App\Http\Controllers\ImageUploadController::class, 'delete'])->name('image.delete');
    Route::get('/irt', [App\Http\Controllers\irtController::class, 'index'])->name('irt');
    Route::post('/irt/analyze', [App\Http\Controllers\irtController::class, 'analyze'])->name('irt.analyze');

Route::get('/editor', function () {
    return view('editor');
});

Route::post('/upload-image', [App\Http\Controllers\CKEditorController::class, 'upload'])->name('ckeditor.upload');
Route::post('/ckeditor/delete', [App\Http\Controllers\CkeditorController::class, 'delete'])->name('ckeditor.delete');

Route::post('/check-password', [App\Http\Controllers\DashboardSettingController::class, 'checkPassword'])->name('password.check');

Route::group(['middleware' => ['auth']], function() {
     Route::get('/register-profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
     Route::post('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])
     ->name('profile.update');
     Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
     Route::get('/dashboard/edit-major', [App\Http\Controllers\UserMajorController::class, 'index'])->name('user.majors.index');;
     Route::post('/dashboard/edit-major', [App\Http\Controllers\UserMajorController::class, 'store'])->name('user.majors.store');
     Route::get('/dashboard/edit-major/by-university/{id}', [App\Http\Controllers\UserMajorController::class, 'getMajorsByUniversity']);

    Route::delete('/dashboard/edit-major/{id}', [App\Http\Controllers\UserMajorController::class, 'destroy'])->name('user.majors.destroy');
    Route::get('/dashboard/get-majors/{university_id}', [App\Http\Controllers\UserMajorController::class, 'getMajorsByUniversity']);


    Route::get('/bank-soal/statistics', [App\Http\Controllers\BankSoalController::class, 'statistics'])->name('bank-soal.statistics');
     Route::get('/bank-soal/history', [App\Http\Controllers\BankSoalController::class, 'history'])->name('bank-soal.history');
    //get-exam bank soal
     Route::get('/banksoal/{exam}/result/{id}', [App\Http\Controllers\BanksoalController::class, 'result'])->name('bank-soal-result');
     Route::post('/get-exam', [App\Http\Controllers\ExamController::class, 'submit'])->name('exam-submit');
// Route untuk generate ulang AI (akan selalu membuat record baru)
     Route::post('/banksoal/{exam}/result/{id}/generate-ai', [App\Http\Controllers\BanksoalController::class, 'generateAI'])
     ->name('bank-soal-generate-ai');
     Route::get('/banksoal/{exam}/review/{question}', [App\Http\Controllers\BanksoalController::class, 'review'])->name('exam.review');



Route::get('/get-tryout-detail/{slug}', [App\Http\Controllers\TryoutController::class, 'show'])->name('tryout-detail');
    Route::get('/get-tryout/{exam}/{subtest}', [App\Http\Controllers\TryoutExamController::class, 'exam'])->name('tryout-subtest');
     Route::post('/get-tryout/{exam}/{subtest}', [App\Http\Controllers\TryoutExamController::class, 'submit'])
          ->name('tryout-submit');
     Route::get('/get-tryout/{exam}/{subCategory}/interstitial', [App\Http\Controllers\TryoutExamController::class, 'interstitial'])
          ->name('subtest-interstitial');
     Route::get('/get-tryout/{exam}/finish', [App\Http\Controllers\TryoutExamController::class, 'finish'])
          ->name('tryout-finish');
     Route::get('/get-tryout/{exam}/result/{id}', [App\Http\Controllers\TryoutController::class, 'result'])->name('tryout-result');
      Route::get('/get-tryout/{exam}/result/{result}/download-pdf', [App\Http\Controllers\TryoutController::class, 'downloadResultPdf'])
    ->name('tryout.download-pdf');
     Route::get('/get-tryout/{exam}/review/{subCategory}/{question}', [App\Http\Controllers\TryoutController::class, 'review'])->name('tryout.review');
     Route::get('/get-tryout/{exam}/result/{id}/leaderboard', [App\Http\Controllers\TryoutResultsController::class, 'leaderboard'])->name('tryout-leaderboard');
     Route::get('/get-tryout/{exam}/result/{id}/evaluation', [App\Http\Controllers\TryoutResultsController::class, 'evaluation'])->name('tryout-evaluation');
     Route::get('/get-tryout/{exam}/result/{id}/reccomendation', [App\Http\Controllers\TryoutResultsController::class, 'recommendation'])->name('tryout-recommendation');

     Route::get('/register-iti/{result_id}', [App\Http\Controllers\RegistrationITIController::class, 'create'])->name('register-iti.create');
     Route::post('/register-iti', [App\Http\Controllers\RegistrationITIController::class, 'store'])->name('register-iti.store');

 
     // Route untuk generate ulang recommendation AI
     Route::post('/exams/{exam}/results/{id}/recommendation/generate-recommendation', [App\Http\Controllers\TryoutResultsController::class, 'generateRecommendation'])
          ->name('tryout-generate-recommendation');
     Route::post('/exams/{exam}/results/{id}/evaluation/generate-evaluation', [App\Http\Controllers\TryoutResultsController::class, 'generateEvaluation'])
          ->name('tryout-generate-evaluation');
     Route::get('/get-tryout/{exam}/result/{id}/ranking-university', [App\Http\Controllers\TryoutResultsController::class, 'university'])->name('tryout-university');
    Route::get('/register/success', [App\Http\Controllers\Auth\RegisterController::class, 'success'])->name('register-success');
    Route::get('/dashboard/settings', [App\Http\Controllers\DashboardSettingController::class, 'store'])->name('dashboard-settings-store');
   Route::post('/dashboard/settings', [App\Http\Controllers\DashboardSettingController::class, 'updatePassword'])->name('dashboard-settings-update');
    Route::get('/dashboard/account', [App\Http\Controllers\DashboardSettingController::class, 'account'])->name('dashboard-settings-account');
    Route::post('/dashboard/account/{redirect}', [App\Http\Controllers\DashboardSettingController::class, 'update'])->name('dashboard-settings-redirect');


     Route::resource('exam', \App\Http\Controllers\Admin\ExamController::class);
    Route::resource('tryout', \App\Http\Controllers\Admin\TryoutController::class);

});
//
Route::prefix('kontributor')->group(function () {
     Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'kontributor'])-> name('kontributor-dashboard');
    // Resource routes
   

    // Soal
    Route::resource('question', \App\Http\Controllers\Admin\QuestionController::class);
      Route::get('/question/import/{exam_id}', [\App\Http\Controllers\Admin\QuestionController::class, 'showForm'])->name('questions.import.form');
Route::post('/question/import/{exam_id}', [\App\Http\Controllers\Admin\QuestionController::class, 'import'])->name('questions.import');
Route::get('/questions/download-template', [\App\Http\Controllers\Admin\QuestionController::class, 'downloadTemplate'])->name('questions.download-template');
  

});
Route::prefix('validator')->group(function () {
     Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'validator'])-> name('validator-dashboard');
    // Resource routes
  

});

Route::prefix('sales')->group(function () {
     Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'sales'])-> name('sales-dashboard');
    // Resource routes
   

    // pendaftar
    Route::resource('pendaftar', \App\Http\Controllers\Admin\PendaftarController::class);
  

});


Route::prefix('admin')
->namespace('Admin')
->middleware(['auth','admin'])
->group(function() {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index']) -> name('admin-dashboard');
    route::resource('category', '\App\Http\Controllers\Admin\CategoryController'); 
    route::resource('subcategory', '\App\Http\Controllers\Admin\SubCategoryController'); 
    route::resource('university', '\App\Http\Controllers\Admin\UniversityController'); 
    route::resource('major', '\App\Http\Controllers\Admin\MajorController'); 
    route::resource('user', '\App\Http\Controllers\Admin\UserController'); 
    route::resource('blog', '\App\Http\Controllers\Admin\BlogController'); 
    Route::resource('nilai-tryout', '\App\Http\Controllers\Admin\TryoutScoreController');

    // Halaman IRT Analysis
    Route::get('/nilai-tryout/{slug}/irt-analysis', [\App\Http\Controllers\Admin\TryoutScoreController::class, 'show'])
         ->name('irt.show');

    // Hitung IRT (AJAX)
    Route::post('/nilai-tryout/{slug}/calculate-irt', [\App\Http\Controllers\Admin\TryoutScoreController::class, 'calculateIRT'])
         ->name('irt.calculate');

    // Reset nilai (AJAX)
    Route::post('/nilai-tryout/{slug}/reset-scores', [\App\Http\Controllers\Admin\TryoutScoreController::class, 'resetScores'])
         ->name('irt.reset');

    // Detail skor per kategori
    Route::get('/nilai-tryout/{slug}/detailed-scores/{categoryName}', [\App\Http\Controllers\Admin\TryoutScoreController::class, 'detailedScores'])
         ->name('irt.detailed-scores');

    // Response data per kategori (AJAX)
    Route::get('/nilai-tryout/{slug}/response-data/{category}', [\App\Http\Controllers\Admin\TryoutScoreController::class, 'responseData'])
         ->name('irt.response-data');

});
Auth::routes();

