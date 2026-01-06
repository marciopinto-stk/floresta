<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class ModulesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $modulesPath = base_path('src/Modules');

        foreach(File::directories($modulesPath) as $module) {
            $provider = $module . '/Providers/' . basename($module) . 'ServiceProvider.php';

            if (File::exists($provider)) {
                $namespace = 'App\\Modules\\' . basename($module) . '\\Providers\\' . basename($module) . 'ServiceProvider';
                if (class_exists($namespace)) {
                    $this->app->register($namespace);
                }
            }
        }
    }
}
