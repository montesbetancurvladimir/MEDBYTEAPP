<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Google\GoogleExtendSocialite;

class RouteServiceProvider extends ServiceProvider
{

    public const HOME = '/home';
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        // Registrar el proveedor de Google
        $this->app['events']->listen(
            SocialiteWasCalled::class,
            function (SocialiteWasCalled $socialiteWasCalled) {
                $socialiteWasCalled->extendSocialite('google', GoogleExtendSocialite::class);
            }
        );
    }
}
