<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Services\AIEvaluationService;
use App\Services\AIEvaluationService1;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Daftarkan AI service sebagai singleton
        $this->app->singleton(AIEvaluationService::class, function ($app) {
            return new AIEvaluationService();
        });
        $this->app->singleton(AIEvaluationService1::class, function ($app) {
            return new AIEvaluationService1();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        config(['app.locale' => 'id']);
        Carbon::setLocale('id');
        date_default_timezone_set('Asia/Jakarta');

        Blade::if('role', function ($role) {
        return auth()->check() && auth()->user()->role === strtoupper($role);
    });
    }
}
