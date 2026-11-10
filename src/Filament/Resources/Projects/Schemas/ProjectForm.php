<?php

namespace MikePageDev\FilamentPortfolio\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Details')->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(150)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Set $set, ?string $state, string $operation): void {
                            if ($operation === 'create') {
                                $set('slug', Str::slug($state ?? ''));
                            }
                        }),
                    TextInput::make('slug')
                        ->required()
                        ->maxLength(150)
                        ->regex('/^[a-z0-9-]+$/')
                        ->unique(ignoreRecord: true)
                        ->helperText('Lowercase letters, numbers and dashes. Used in the page address.'),
                    Textarea::make('summary')->rows(3),
                    Toggle::make('is_published')->label('Published'),
                ]),
                Section::make('Case study')->schema([
                    Textarea::make('problem')->label('The problem')->rows(4),
                    Textarea::make('role')->label('Role')->rows(3),
                    Repeater::make('case_study')
                        ->label('Sections')
                        ->schema([
                            TextInput::make('heading')->required()->maxLength(150),
                            Textarea::make('body')->required()->rows(5),
                        ])
                        ->reorderable()
                        ->collapsible()
                        ->defaultItems(0),
                ]),
                Section::make('Technologies')->schema([
                    TagsInput::make('tech')
                        ->label('Technologies')
                        ->nestedRecursiveRules(['max:50']),
                ]),
                Section::make('Screenshots')->schema([
                    Repeater::make('images')
                        ->label('Screenshots')
                        ->schema([
                            FileUpload::make('path')
                                ->label('Image')
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif', 'image/gif'])
                                ->required()
                                ->maxSize(5120)
                                ->helperText('JPEG, PNG, WebP, AVIF or GIF, up to 5 MB.')
                                ->disk(config()->string('filament-portfolio.disk'))
                                ->directory(config()->string('filament-portfolio.directory'))
                                ->visibility('public'),
                            TextInput::make('alt')
                                ->label('Alt text')
                                ->required()
                                ->maxLength(150),
                        ])
                        ->reorderable()
                        ->defaultItems(0),
                ]),
                Section::make('Links')->schema([
                    TextInput::make('live_url')
                        ->label('Live site URL')
                        ->url()
                        ->startsWith(['https://'])
                        ->maxLength(255),
                    TextInput::make('source_url')
                        ->label('Source code URL')
                        ->url()
                        ->startsWith(['https://'])
                        ->maxLength(255),
                ]),
            ]);
    }
}
