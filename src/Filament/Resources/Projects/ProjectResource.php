<?php

namespace MikePageDev\FilamentPortfolio\Filament\Resources\Projects;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use MikePageDev\FilamentPortfolio\Filament\Resources\Projects\Pages\CreateProject;
use MikePageDev\FilamentPortfolio\Filament\Resources\Projects\Pages\EditProject;
use MikePageDev\FilamentPortfolio\Filament\Resources\Projects\Pages\ListProjects;
use MikePageDev\FilamentPortfolio\Filament\Resources\Projects\Schemas\ProjectForm;
use MikePageDev\FilamentPortfolio\Filament\Resources\Projects\Tables\ProjectsTable;
use MikePageDev\FilamentPortfolio\Models\Project;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $navigationLabel = 'Projects';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return ProjectForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }
}
