<?php namespace Williamoliveira\Attachable\Providers;

use Illuminate\Support\ServiceProvider;
use Williamoliveira\Attachable\Contracts\AttachableHelpersContract;
use Williamoliveira\Attachable\Contracts\InterpolatorContract;
use Williamoliveira\Attachable\Contracts\Processors\FileProcessorContract;
use Williamoliveira\Attachable\Contracts\Processors\InterventionImageProcessorContract;
use Williamoliveira\Attachable\Contracts\StorageContract;
use Williamoliveira\Attachable\Observers\AttachableModelObserver;
use Williamoliveira\Attachable\Processors\FileProcessor;
use Williamoliveira\Attachable\Processors\InterventionImageProcessor;
use Williamoliveira\Attachable\Services\AttachableHelpers;
use Williamoliveira\Attachable\Services\Interpolator;
use Williamoliveira\Attachable\Services\RemoteFileManager;
use Williamoliveira\Attachable\Storage\LaravelFilesystem;

/**
 * Class AttachableServiceProvider
 * @package Williamoliveira\Attachable\Providers
 */
class AttachableServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $base      = __DIR__ . '/../../resources/';
        $migration = '2015_01_01_000000_create_attachable_table.php';

        $this->publishes([
            ($base . 'config/attachable.php') => config_path('attachable.php'),
            ($base . 'database/migrations/' . $migration) => base_path('database/migrations/' . $migration)
        ]);

        $this->mergeConfigFrom(
            $base . '/config/attachable.php', 'attachable'
        );

        $this->registerObserver();
    }

    public function register()
    {

        $this->app->singleton(
            'attachable.storage',
            LaravelFilesystem::class
        );

        $this->app->singleton(
            'attachable.remotefile',
            RemoteFileManager::class
        );

        $this->app->singleton(
            'attachable.helpers',
            AttachableHelpers::class
        );

        $this->app->singleton(
            'attachable.interpolator',
            Interpolator::class
        );

        $this->app->singleton(
            'attachable.processors.file',
            FileProcessor::class
        );

        $this->app->singleton(
            'attachable.processors.intervention',
            InterventionImageProcessor::class
        );

        $this->app->bind(
            StorageContract::class,
            'attachable.storage'
        );
        
        $this->app->bind(
            InterventionImageProcessorContract::class,
            'attachable.processors.intervention'
        );

        $this->app->bind(
            FileProcessorContract::class,
            'attachable.processors.file'
        );

        $this->app->bind(
            InterpolatorContract::class,
            'attachable.interpolator'
        );

        $this->app->bind(
            AttachableHelpersContract::class,
            'attachable.helpers'
        );
    }

    private function registerObserver()
    {
        $models = config('attachable.models');

        foreach($models as $model) {
            $model::observe($this->app->make(AttachableModelObserver::class));
        }
    }

}