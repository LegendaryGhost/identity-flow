<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
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
        if (!Cache::has('duree_vie_pin')) {
            Cache::forever('duree_vie_pin', env('DUREE_VIE_PIN', 90));
        }

        if (!Cache::has('duree_vie_token')) {
            Cache::forever('duree_vie_token', env('DUREE_VIE_TOKEN', 2592000));
        }

        if (!Cache::has('duree_vie_tentative')) {
            Cache::forever('duree_vie_tentative', env('DUREE_VIE_TENTATIVE', 86400));
        }

        if (!Cache::has('nombre_tentative')) {
            Cache::forever('nombre_tentative', env('NOMBRE_TENTATIVE', 3));
        }

        if (!Cache::has('DUREE_VIE_LIEN_INSCRIPTION')) {
            Cache::forever('duree_vie_inscription', env('DUREE_VIE_LIEN_INSCRIPTION', 90));
        }

        if (!Cache::has('FIREBASE_KEY')) {
            Cache::forever('firebase_key', env('FIREBASE_KEY', ''));
        }

        if (!Cache::has('FIREBASE_APP_ID')) {
            Cache::forever('firebase_app_id', env('FIREBASE_APP_ID', ''));
        }
    }
}
