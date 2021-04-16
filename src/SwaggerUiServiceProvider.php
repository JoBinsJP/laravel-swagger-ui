<?php

namespace Jobins\SwaggerUi;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Jobins\SwaggerUi\Console\InstallCommand;
use Jobins\SwaggerUi\Http\Controllers\OpenApiJsonController;
use Jobins\SwaggerUi\Http\Middleware\EnsureUserIsAuthorized;

class SwaggerUiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'swagger-ui');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/swagger-ui.php' => config_path('swagger-ui.php'),
            ], 'swagger-ui-config');

            $this->publishes([
                __DIR__.'/../stubs/SwaggerUiServiceProvider.stub' => app_path('Providers/SwaggerUiServiceProvider.php'),
            ], 'swagger-ui-provider');
        }

        $this->loadRoutes();
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/swagger-ui.php', 'swagger-ui');

        $this->commands([InstallCommand::class]);
    }

    /**
     * Load the Swagger UI routes.
     *
     * @return void
     */
    protected function loadRoutes()
    {
        Route::middleware(['web', EnsureUserIsAuthorized::class])
            ->prefix(config('swagger-ui.path'))
            ->group(function () {
                Route::view('/', 'swagger-ui::index')->name('swagger-ui');

                Route::get('openapi.json', OpenApiJsonController::class)->name('swagger-openapi-json');
            });
    }
}
