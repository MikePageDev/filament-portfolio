<?php

namespace MikePageDev\FilamentPortfolio\Tests;

class PackageTest extends TestCase
{
    public function test_config_is_merged(): void
    {
        $this->assertSame('public', config('filament-portfolio.disk'));
        $this->assertSame('portfolio', config('filament-portfolio.directory'));
    }
}
