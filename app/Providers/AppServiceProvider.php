<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        // $this->app->singleton(
        $loader->alias(
            'LaravelPWA\Services\ManifestService',
            'App\Providers\MyPwaServiceProvider'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        if (Schema::hasTable('pengaturan')) {
            $pengaturan = DB::table('pengaturan')->where('key','nama')->first();
            if ($pengaturan) {
                $nama = $pengaturan->value;
            } else {
                $nama = 'Sistem SPP';
            }
            View::share('sitename', $nama);
        }
        
    }
}
