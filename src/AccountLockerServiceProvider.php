<?php namespace Crazymeeks\AccountLocker;

use Illuminate\Support\ServiceProvider;
use App\User;
class AccountLockerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/accountlocker.php' => config_path('accountlocker.php'),
        ], 'config');
        //include __DIR__ . './routes.php';
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // package config
        $this->mergeConfigFrom(__DIR__ . '/config/accountlocker.php', 'accountlocker');

        $this->app['accountlocker'] = $this->app->share(function($app){
            return new AccountLocker(new User);
        });
    }
}
