<?php

namespace App\Providers;

use App\Models\Configuration;
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
            $configuration = Configuration::where("cle", "duree_vie_pin")->first();
            Cache::forever('duree_vie_pin', $configuration->valeur);
        }
        if (!Cache::has('duree_vie_token')) {
            $configuration = Configuration::where("cle", "duree_vie_token")->first();
            Cache::forever('duree_vie_token', $configuration->valeur);
        }
        if (!Cache::has('duree_vie_tentative')) {
            $configuration = Configuration::where("cle", "duree_vie_tentative")->first();
            Cache::forever('duree_vie_tentative', $configuration->valeur);
        }
        if (!Cache::has('nombre_tentative')) {
            $configuration = Configuration::where("cle", "nombre_tentative")->first();
            Cache::forever('nombre_tentative', $configuration->valeur);
        }
    }
}
