<?php

namespace admin\pages;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PagesModuleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load routes, views, migrations from the package  
        $this->loadViewsFrom([
            base_path('Modules/Pages/resources/views'), // Published module views first
            resource_path('views/admin/page'), // Published views second
            __DIR__ . '/../resources/views'      // Package views as fallback
        ], 'page');

        // Load published module config first (if it exists), then fallback to package config
        if (file_exists(base_path('Modules/Pages/config/pages.php'))) {
            $this->mergeConfigFrom(base_path('Modules/Pages/config/pages.php'), 'pages.constants');
        } else {
            // Fallback to package config if published config doesn't exist
            $this->mergeConfigFrom(__DIR__.'/../config/pages.php', 'pages.constants');
        }
        
        // Also register module views with a specific namespace for explicit usage
        if (is_dir(base_path('Modules/Pages/resources/views'))) {
            $this->loadViewsFrom(base_path('Modules/Pages/resources/views'), 'pages-module');
        }
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        // Also load migrations from published module if they exist
        if (is_dir(base_path('Modules/Pages/database/migrations'))) {
            $this->loadMigrationsFrom(base_path('Modules/Pages/database/migrations'));
        }
        
        // Only publish automatically during package installation, not on every request
        // Use 'php artisan pages:publish' command for manual publishing
        // $this->publishWithNamespaceTransformation();
        
        // Standard publishing for non-PHP files
        $this->publishes([
            __DIR__ . '/../config/' => base_path('Modules/Pages/config/'),
            __DIR__ . '/../database/migrations' => base_path('Modules/Pages/database/migrations'),
            __DIR__ . '/../resources/views' => base_path('Modules/Pages/resources/views/'),
        ], 'page');
       
        $this->registerAdminRoutes();
    }

    protected function registerAdminRoutes()
    {
        if (!Schema::hasTable('admins')) {
            return; // Avoid errors before migration
        }

        $slug = DB::table('admins')->latest()->value('website_slug') ?? 'admin';

        Route::middleware('web')
            ->prefix("{$slug}/admin") // dynamic prefix
            ->group(function () {
                // Load routes from published module first, then fallback to package
                if (file_exists(base_path('Modules/Pages/routes/web.php'))) {
                    $this->loadRoutesFrom(base_path('Modules/Pages/routes/web.php'));
                } else {
                    $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
                }
            });
    }

    public function register()
    {
        // Register the publish command
        if ($this->app->runningInConsole()) {
            $this->commands([
                \admin\pages\Console\Commands\PublishPagesModuleCommand::class,
                \admin\pages\Console\Commands\CheckModuleStatusCommand::class,
                \admin\pages\Console\Commands\DebugPagesCommand::class,
                \admin\pages\Console\Commands\TestViewResolutionCommand::class,
            ]);
        }
    }

    /**
     * Publish files with namespace transformation
     */
    protected function publishWithNamespaceTransformation()
    {
        // Define the files that need namespace transformation
        $filesWithNamespaces = [
            // Controllers
            __DIR__ . '/../src/Controllers/PageManagerController.php' => base_path('Modules/Pages/app/Http/Controllers/Admin/PageManagerController.php'),
            
            // Models
            __DIR__ . '/../src/Models/Page.php' => base_path('Modules/Pages/app/Models/Page.php'),
            
            // Requests
            __DIR__ . '/../src/Requests/PageCreateRequest.php' => base_path('Modules/Pages/app/Http/Requests/PageCreateRequest.php'),
            __DIR__ . '/../src/Requests/PageUpdateRequest.php' => base_path('Modules/Pages/app/Http/Requests/PageUpdateRequest.php'),
            
            // Routes
            __DIR__ . '/routes/web.php' => base_path('Modules/Pages/routes/web.php'),
        ];

        foreach ($filesWithNamespaces as $source => $destination) {
            if (File::exists($source)) {
                // Create destination directory if it doesn't exist
                File::ensureDirectoryExists(dirname($destination));
                
                // Read the source file
                $content = File::get($source);
                
                // Transform namespaces based on file type
                $content = $this->transformNamespaces($content, $source);
                
                // Write the transformed content to destination
                File::put($destination, $content);
            }
        }
    }

    /**
     * Transform namespaces in PHP files
     */
    protected function transformNamespaces($content, $sourceFile)
    {
        // Define namespace mappings
        $namespaceTransforms = [
            // Main namespace transformations
            'namespace admin\\pages\\Controllers;' => 'namespace Modules\\Pages\\app\\Http\\Controllers\\Admin;',
            'namespace admin\\pages\\Models;' => 'namespace Modules\\Pages\\app\\Models;',
            'namespace admin\\pages\\Requests;' => 'namespace Modules\\Pages\\app\\Http\\Requests;',
            
            // Use statements transformations
            'use admin\\pages\\Controllers\\' => 'use Modules\\Pages\\app\\Http\\Controllers\\Admin\\',
            'use admin\\pages\\Models\\' => 'use Modules\\Pages\\app\\Models\\',
            'use admin\\pages\\Requests\\' => 'use Modules\\Pages\\app\\Http\\Requests\\',
            
            // Class references in routes
            'admin\\pages\\Controllers\\PageManagerController' => 'Modules\\Pages\\app\\Http\\Controllers\\Admin\\PageManagerController',
        ];

        // Apply transformations
        foreach ($namespaceTransforms as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }

        // Handle specific file types
        if (str_contains($sourceFile, 'Controllers')) {
            $content = $this->transformControllerNamespaces($content);
        } elseif (str_contains($sourceFile, 'Models')) {
            $content = $this->transformModelNamespaces($content);
        } elseif (str_contains($sourceFile, 'Requests')) {
            $content = $this->transformRequestNamespaces($content);
        } elseif (str_contains($sourceFile, 'routes')) {
            $content = $this->transformRouteNamespaces($content);
        }

        return $content;
    }

    /**
     * Transform controller-specific namespaces
     */
    protected function transformControllerNamespaces($content)
    {
        // Update use statements for models and requests
        $content = str_replace(
            'use admin\\pages\\Models\\Page;',
            'use Modules\\Pages\\app\\Models\\Page;',
            $content
        );
        
        $content = str_replace(
            'use admin\\pages\\Requests\\PageCreateRequest;',
            'use Modules\\Pages\\app\\Http\\Requests\\PageCreateRequest;',
            $content
        );
        
        $content = str_replace(
            'use admin\\pages\\Requests\\PageUpdateRequest;',
            'use Modules\\Pages\\app\\Http\\Requests\\PageUpdateRequest;',
            $content
        );

        $content = str_replace(
            'use admin\admin_auth\Traits\HasSeo;',
            'use Modules\\AdminAuth\\app\\Traits\\HasSeo;',
            $content
        );

        return $content;
    }

    /**
     * Transform model-specific namespaces
     */
    protected function transformModelNamespaces($content)
    {
        // Any model-specific transformations
        $content = str_replace(
            'use admin\admin_auth\Models\Seo;',
            'use Modules\\AdminAuth\\app\\Models\\Seo;',
            $content
        );
        return $content;
    }

    /**
     * Transform request-specific namespaces
     */
    protected function transformRequestNamespaces($content)
    {
        // Any request-specific transformations
        return $content;
    }

    /**
     * Transform route-specific namespaces
     */
    protected function transformRouteNamespaces($content)
    {
        // Update controller references in routes
        $content = str_replace(
            'admin\\pages\\Controllers\\PageManagerController',
            'Modules\\Pages\\app\\Http\\Controllers\\Admin\\PageManagerController',
            $content
        );

        return $content;
    }
}