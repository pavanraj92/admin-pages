<?php

namespace admin\pages\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishPagesModuleCommand extends Command
{
    protected $signature = 'pages:publish {--force : Force overwrite existing files}';
    protected $description = 'Publish Pages module files with proper namespace transformation';

    public function handle()
    {
        $this->info('Publishing Pages module files...');

        // Check if module directory exists
        $moduleDir = base_path('Modules/Pages');
        if (!File::exists($moduleDir)) {
            File::makeDirectory($moduleDir, 0755, true);
        }

        // Publish with namespace transformation
        $this->publishWithNamespaceTransformation();
        
        // Publish other files
        $this->call('vendor:publish', [
            '--tag' => 'page',
            '--force' => $this->option('force')
        ]);

        // Update composer autoload
        $this->updateComposerAutoload();

        $this->info('Pages module published successfully!');
        $this->info('Please run: composer dump-autoload');
    }

    protected function publishWithNamespaceTransformation()
    {
        $basePath = dirname(dirname(__DIR__)); // Go up to packages/admin/pages/src
        
        $filesWithNamespaces = [
            // Controllers
            $basePath . '/Controllers/PageManagerController.php' => base_path('Modules/Pages/app/Http/Controllers/Admin/PageManagerController.php'),
            
            // Models
            $basePath . '/Models/Page.php' => base_path('Modules/Pages/app/Models/Page.php'),
            
            // Requests
            $basePath . '/Requests/PageCreateRequest.php' => base_path('Modules/Pages/app/Http/Requests/PageCreateRequest.php'),
            $basePath . '/Requests/PageUpdateRequest.php' => base_path('Modules/Pages/app/Http/Requests/PageUpdateRequest.php'),
            
            // Routes
            $basePath . '/routes/web.php' => base_path('Modules/Pages/routes/web.php'),
        ];

        foreach ($filesWithNamespaces as $source => $destination) {
            if (File::exists($source)) {
                File::ensureDirectoryExists(dirname($destination));
                
                $content = File::get($source);
                $content = $this->transformNamespaces($content, $source);
                
                File::put($destination, $content);
                $this->info("Published: " . basename($destination));
            } else {
                $this->warn("Source file not found: " . $source);
            }
        }
    }

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
            $content = str_replace('use admin\\pages\\Models\\Page;', 'use Modules\\Pages\\app\\Models\\Page;', $content);
            $content = str_replace('use admin\\pages\\Requests\\PageCreateRequest;', 'use Modules\\Pages\\app\\Http\\Requests\\PageCreateRequest;', $content);
            $content = str_replace('use admin\\pages\\Requests\\PageUpdateRequest;', 'use Modules\\Pages\\app\\Http\\Requests\\PageUpdateRequest;', $content);
        }

        return $content;
    }

    protected function updateComposerAutoload()
    {
        $composerFile = base_path('composer.json');
        $composer = json_decode(File::get($composerFile), true);

        // Add module namespace to autoload
        if (!isset($composer['autoload']['psr-4']['Modules\\Pages\\'])) {
            $composer['autoload']['psr-4']['Modules\\Pages\\'] = 'Modules/Pages/app/';
            
            File::put($composerFile, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $this->info('Updated composer.json autoload');
        }
    }
}
