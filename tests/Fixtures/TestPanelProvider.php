<?php

namespace MikePageDev\FilamentPortfolio\Tests\Fixtures;

use Filament\Panel;
use Filament\PanelProvider;
use MikePageDev\FilamentPortfolio\FilamentPortfolioPlugin;

class TestPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->plugin(FilamentPortfolioPlugin::make());
    }
}
