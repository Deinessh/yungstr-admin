<?php

namespace App\Providers;

use App\Services\MailConfigService;
use App\Services\SettingService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (! $this->settingsTableExists()) {
            return;
        }

        app(MailConfigService::class)->apply();

        View::composer('*', function ($view) {
            $settings = app(SettingService::class);
            $view->with('storeSettings', $settings->allPublic());
            $view->with('navCategories', \App\Models\Category::orderBy('name')->get());

            $page = match (true) {
                request()->routeIs('home') => 'home',
                request()->routeIs('products.*') => 'catalogue',
                request()->routeIs('about') => 'about',
                request()->routeIs('contact') => 'contact',
                default => null,
            };

            $view->with('pageSeo', $page ? $settings->seoForPage($page) : []);
        });
    }

    private function settingsTableExists(): bool
    {
        try {
            return Schema::hasTable('settings');
        } catch (\Throwable) {
            return false;
        }
    }
}
