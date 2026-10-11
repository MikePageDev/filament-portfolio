<?php

namespace MikePageDev\FilamentPortfolio;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentPortfolioServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-portfolio')
            ->hasConfigFile()
            ->hasMigration('create_portfolio_projects_table');
    }
}
