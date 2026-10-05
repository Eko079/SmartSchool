<?php

namespace App\Providers;

use App\Models\SchoolSetting;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Paksa skema https di production agar url() tidak hasilkan http (anti mixed-content).
        if (config('app.env') === 'production' || ($this->app->request && $this->app->request->header('X-Forwarded-Proto') === 'https')) {
            URL::forceScheme('https');
        }

        // Branding global dari Pengaturan (dipakai login, footer, sidebar).
        // Composer per-render agar perubahan Pengaturan langsung tampil (View::share terkunci saat boot).
        View::composer('*', function ($view) {
            try {
                $settings = SchoolSetting::pluck('value', 'key')->toArray();
            } catch (\Throwable $e) {
                $settings = [];
            }
            try {
                $totalStudents = \App\Models\Student::count();
                $totalClasses = \App\Models\ClassRoom::count();
            } catch (\Throwable $e) {
                $totalStudents = 0;
                $totalClasses = 0;
            }
            $view->with('siteBrand', [
                'school_name' => $settings['school_name'] ?? 'SmartSchool',
                'school_email' => $settings['school_email'] ?? 'support@smartschool.id',
                'school_phone' => $settings['school_phone'] ?? '',
                'academic_year' => $settings['academic_year'] ?? '',
                'semester' => $settings['active_semester'] ?? 'Ganjil',
                'version' => config('app.version', 'v2.4.1'),
                'total_students' => $totalStudents,
                'total_classes' => $totalClasses,
            ]);
        });
    }
}
