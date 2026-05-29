<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use App\Services\NavigationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(NavigationService::class, function () {
            return new NavigationService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share $nav with ALL blade views automatically
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $view->with('nav', app(NavigationService::class));
            }
        });

        // @canNav('permission') ... @endCanNav
        // Completely hides element if no permission
        Blade::directive('canNav', function ($permission) {
            return "<?php if(isset(\$nav) && \$nav->can({$permission})): ?>";
        });

        Blade::directive('endCanNav', function () {
            return "<?php endif; ?>";
        });

        // @canNavAny(['perm1', 'perm2']) ... @endCanNavAny
        // Shows if user has ANY of the listed permissions
        Blade::directive('canNavAny', function ($permissions) {
            return "<?php if(isset(\$nav) && \$nav->canAny({$permissions})): ?>";
        });

        Blade::directive('endCanNavAny', function () {
            return "<?php endif; ?>";
        });
    }
}
