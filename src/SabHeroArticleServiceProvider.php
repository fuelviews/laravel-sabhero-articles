<?php

namespace Fuelviews\SabHeroArticle;

use Fuelviews\SabHeroArticle\Components\Breadcrumb;
use Fuelviews\SabHeroArticle\Components\Card;
use Fuelviews\SabHeroArticle\Components\FeatureCard;
use Fuelviews\SabHeroArticle\Components\HeaderCategory;
use Fuelviews\SabHeroArticle\Components\Layout;
use Fuelviews\SabHeroArticle\Components\Markdown;
use Fuelviews\SabHeroArticle\Components\RecentPost;
use Fuelviews\SabHeroArticle\Http\Livewire\SearchAutocomplete;
use Fuelviews\SabHeroArticle\Models\Page;
use Fuelviews\SabHeroArticle\Models\Post;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Spatie\Feed\FeedServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SabHeroArticleServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('sabhero-article')
            ->hasConfigFile([
                'sabhero-article',
                'feed',
            ])
            ->hasMigrations([
                'add_author_fields_to_users_table',
                'create_article_tables',
                'create_media_table',
                'create_imports_table',
                'create_exports_table',
                'create_failed_import_rows_table',
            ])
            ->hasViewComponents(
                'sabhero-article',
                Layout::class,
                RecentPost::class,
                HeaderCategory::class,
                FeatureCard::class,
                Card::class,
                Markdown::class,
                Breadcrumb::class,
            )
            ->hasViews('sabhero-article')
            ->hasRoutes([
                'web',
                'breadcrumbs',
            ]);
        // $this->loadTestingMigration();
    }

    public function register()
    {
        Paginator::defaultView('sabhero-article::pagination.tailwind');

        Route::bind('post', function ($value) {
            return Post::where('slug', $value)
                ->published()
                ->with(['user', 'categories', 'tags', 'media'])
                ->firstOrFail();
        });

        Route::bind('user', function ($value) {
            $userModel = config('sabhero-article.user.model');

            return $userModel::where('slug', $value)->firstOrFail();
        });

        View::composer([
            '*',
        ], static function ($view) {
            if (request()->route() &&
                in_array(request()->route()->getName(), [
                    'sabhero-article.post.show',
                ])) {
                $seoPost = request()->route('post');

                $view->with([
                    'seoPost' => $seoPost,
                ]);
            }
        });

        $this->app->register(SabHeroArticleEventServiceProvider::class);

        return parent::register();
    }

    public function bootingPackage(): void
    {
        View::composer('*', function ($view) {
            $routeName = Route::currentRouteName();

            $seoPage = Page::where('slug', $routeName)
                ->first();

            $view->with('seoPage', $seoPage);
        });
    }

    public function packageBooted(): void
    {
        // Register FeedServiceProvider after views are registered
        $this->app->register(FeedServiceProvider::class);
        
        // Register Livewire components if Livewire is available and not in testing
        if (class_exists(\Livewire\Livewire::class) && ! $this->app->environment('testing')) {
            Livewire::component('sabhero-article::search-autocomplete', SearchAutocomplete::class);
        }
    }

    public function loadTestingMigration(): void
    {
        if ($this->app->environment('testing')) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }
}
