<?php

namespace MikePageDev\FilamentPortfolio\Models;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use MikePageDev\FilamentPortfolio\Database\Factories\ProjectFactory;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property ?string $summary
 * @property ?string $problem
 * @property ?string $role
 * @property ?list<string> $tech
 * @property ?list<array{heading: string, body: string}> $case_study
 * @property ?list<array{path: string, alt: string}> $images
 * @property ?string $live_url
 * @property ?string $source_url
 * @property bool $is_published
 * @property int $sort_order
 */
#[Fillable(['title', 'slug', 'summary', 'problem', 'role', 'tech', 'case_study', 'images', 'live_url', 'source_url', 'is_published', 'sort_order'])]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    protected $table = 'portfolio_projects';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tech' => 'array',
            'case_study' => 'array',
            'images' => 'array',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('is_published', true);
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }

    public static function disk(): Filesystem
    {
        return Storage::disk(config()->string('filament-portfolio.disk'));
    }

    protected static function newFactory(): ProjectFactory
    {
        return ProjectFactory::new();
    }

    protected static function booted(): void
    {
        // 'updated' fires before the original attributes are synced, so getOriginal() still holds the old images.
        static::updated(function (Project $project): void {
            $removed = array_diff(
                self::imagePaths($project->getOriginal('images')),
                self::imagePaths($project->images),
            );

            if ($removed !== []) {
                self::disk()->delete(array_values($removed));
            }
        });

        static::deleted(function (Project $project): void {
            $paths = self::imagePaths($project->images);

            if ($paths !== []) {
                self::disk()->delete($paths);
            }
        });
    }

    /**
     * @return list<string>
     */
    private static function imagePaths(mixed $images): array
    {
        if (! is_array($images)) {
            return [];
        }

        $paths = [];

        foreach ($images as $image) {
            if (is_array($image) && isset($image['path']) && is_string($image['path'])) {
                $paths[] = $image['path'];
            }
        }

        return $paths;
    }
}
