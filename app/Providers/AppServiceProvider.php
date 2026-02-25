<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\ClassSubject;
use App\Policies\ClassSubjectPolicy;

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
        // Register policies
        Gate::policy(ClassSubject::class, ClassSubjectPolicy::class);
        Gate::policy(\App\Models\Material::class, \App\Policies\MaterialPolicy::class);
        Gate::policy(\App\Models\Assignment::class, \App\Policies\AssignmentPolicy::class);
        Gate::policy(\App\Models\AcademicEvent::class, \App\Policies\AcademicEventPolicy::class);
    }
}
