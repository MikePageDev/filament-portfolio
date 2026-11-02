<?php

namespace MikePageDev\FilamentPortfolio;

use Filament\Contracts\Plugin;
use Filament\Panel;
use MikePageDev\FilamentPortfolio\Filament\Resources\Projects\ProjectResource;

class FilamentPortfolioPlugin implements Plugin
{
    public static function make(): static
    {
        $plugin = app(static::class);

        assert($plugin instanceof static);

        return $plugin;
    }

    public function getId(): string
    {
        return 'filament-portfolio';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            ProjectResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
