<?php
declare(strict_types=1);

namespace CSCart\Bagisto\Blog\Providers;

use Illuminate\Support\ServiceProvider;

class BlogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'blog');
        $this->mergeConfigFrom(__DIR__ . '/../Config/acl.php', 'acl');
    }
}
