<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;


class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        foreach (File::files(app_path('Repositories')) as $repository)
        {
            $singularName = str_replace('Repository', '', $repository->getFilenameWithoutExtension());

            $repositoryInterfacePath = app_path('Repositories/Contracts/' . $singularName . 'Interface.php');
            $repositoryPath = app_path('Repositories/' . $singularName . 'Repository.php');
            $modelPath = app_path('Models/' . $singularName . '.php');

            if (file_exists($repositoryInterfacePath) && file_exists($repositoryPath)) {
                $interfaceClass = 'App\Repositories\Contracts\\' . $singularName . 'Interface';
                $repositoryClass = 'App\Repositories\\' . $singularName . 'Repository';
                $modelClass = 'App\Models\\' . $singularName;

                if (file_exists($modelPath)) {
                    $this->app->bind($interfaceClass, function ($app) use ($repositoryClass, $modelClass) {
                        return new $repositoryClass(new $modelClass());
                    });
                } else {
                    $this->app->bind($interfaceClass, $repositoryClass);
                }
            }
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // 
    }
}
