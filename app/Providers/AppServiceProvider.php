<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Override;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        // i don't want to produce a mass assignment exception when seeding the database
        Model::unguard();

        // shouldBeStrict allows you to enable strict mode for Eloquent models, which will throw an exception if you try to access a property that doesn't exist on the model.
        Model::shouldBeStrict();

        // automaticlaly eagerLoad
        Model::automaticallyEagerLoadRelationships();
    }
}
