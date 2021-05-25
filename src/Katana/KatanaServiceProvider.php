<?php

namespace Vangarde\Katana;

use Illuminate\Support\ServiceProvider;

class KatanaServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $config_path = __DIR__ . '/../config/katana.php';
        $this->mergeConfigFrom($config_path, 'katana');

        $this->app->bind('view.finder', function ($app) {
            return new ModelViewFinder($app['files'], $app['config']['view.paths']);
        });

        $this->app->singleton('blade.compiler', function ($app) {
            return new KatanaCompiler($app['files'], $app['config']['view.compiled']);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $config_path = __DIR__ . '/../config/katana.php';
        $this->publishes([$config_path => config_path('katana.php')], 'config');
    }

}
