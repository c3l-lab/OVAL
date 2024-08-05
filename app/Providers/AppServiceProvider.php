<?php

namespace oval\Providers;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use oval\Services\InfluxDBService;
use oval\Services\Lti1p3\LtiCache;
use oval\Services\Lti1p3\LtiCookie;
use oval\Services\Lti1p3\LtiDatabase;
use oval\Services\Lti1p3\LtiService;
use oval\Services\YoutubeService;
use Packback\Lti1p3\LtiServiceConnector;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        JWT::$leeway = 5;
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ICache::class, LtiCache::class);
        $this->app->bind(ICache::class, LtiCache::class);
        $this->app->bind(ICookie::class, LtiCookie::class);
        $this->app->bind(IDatabase::class, LtiDatabase::class);
        $this->app->bind(LtiService::class, function () {
            return new LtiService(
                app(IDatabase::class),
                app(ICache::class),
                app(ICookie::class),
                app(ILtiServiceConnector::class)
            );
        });
        $this->app->bind(ILtiServiceConnector::class, function () {
            return new LtiServiceConnector(app(ICache::class), new Client([
                'timeout' => 30,
            ]));
        });
        $this->app->bind(YoutubeService::class, YoutubeService::class);
        $this->app->singleton(InfluxDBService::class, InfluxDBService::class);

        view()->composer('*', function ($view) {
            $user = \Auth::user();
            $theme = $_COOKIE['theme'] ?? 'light';
            $view->with('user', $user)
                ->with('theme', $theme);
        });
    }
}
