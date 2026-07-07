<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('customer.partials.navbar', function ($view) {
            $view->with('navbarCategories', Category::orderBy('name')->get());
        });

        Blade::if('canAccess', function (string $permissionSlug) {
            $user = auth()->user();
            return $user && $user->hasAccess($permissionSlug);
        });

        Blade::if('canFullAccess', function (string $permissionSlug) {
            $user = auth()->user();
            return $user && $user->hasFullAccess($permissionSlug);
        });
    }
}
