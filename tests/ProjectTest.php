<?php

namespace MikePageDev\FilamentPortfolio\Tests;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use MikePageDev\FilamentPortfolio\Models\Project;

class ProjectTest extends TestCase
{
    public function test_json_columns_and_flags_are_cast(): void
    {
        $project = Project::factory()->create([
            'tech' => ['Laravel', 'Filament'],
            'case_study' => [['heading' => 'Approach', 'body' => 'Body.']],
            'images' => [['path' => 'portfolio/a.png', 'alt' => 'A']],
        ])->fresh();

        $this->assertNotNull($project);
        $this->assertSame(['Laravel', 'Filament'], $project->tech);
        $this->assertSame([['heading' => 'Approach', 'body' => 'Body.']], $project->case_study);
        $this->assertSame([['path' => 'portfolio/a.png', 'alt' => 'A']], $project->images);
        $this->assertFalse($project->is_published);
    }

    public function test_published_scope_hides_drafts(): void
    {
        $published = Project::factory()->published()->create();
        Project::factory()->create();

        $this->assertSame([$published->id], Project::query()->published()->pluck('id')->all());
    }

    public function test_ordered_scope_sorts_by_sort_order_then_id(): void
    {
        $c = Project::factory()->create(['sort_order' => 2]);
        $a = Project::factory()->create(['sort_order' => 1]);
        $b = Project::factory()->create(['sort_order' => 1]);

        $this->assertSame([$a->id, $b->id, $c->id], Project::query()->ordered()->pluck('id')->all());
    }

    public function test_slug_is_unique(): void
    {
        Project::factory()->create(['slug' => 'same']);

        $this->expectException(QueryException::class);

        Project::factory()->create(['slug' => 'same']);
    }

    public function test_removed_images_are_deleted_and_kept_images_remain(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('portfolio/keep.png', 'x');
        Storage::disk('public')->put('portfolio/old.png', 'x');

        $project = Project::factory()->create(['images' => [
            ['path' => 'portfolio/keep.png', 'alt' => 'Keep'],
            ['path' => 'portfolio/old.png', 'alt' => 'Old'],
        ]]);

        $project->update(['images' => [['path' => 'portfolio/keep.png', 'alt' => 'Keep']]]);

        Storage::disk('public')->assertExists('portfolio/keep.png');
        Storage::disk('public')->assertMissing('portfolio/old.png');
    }

    public function test_deleting_a_project_deletes_its_images(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('portfolio/a.png', 'x');
        Storage::disk('public')->put('portfolio/b.png', 'x');

        $project = Project::factory()->create(['images' => [
            ['path' => 'portfolio/a.png', 'alt' => 'A'],
            ['path' => 'portfolio/b.png', 'alt' => 'B'],
        ]]);

        $project->delete();

        Storage::disk('public')->assertMissing('portfolio/a.png');
        Storage::disk('public')->assertMissing('portfolio/b.png');
    }
}
