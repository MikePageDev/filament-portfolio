<?php

namespace MikePageDev\FilamentPortfolio\Tests;

use Filament\Forms\Components\Repeater;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use MikePageDev\FilamentPortfolio\Filament\Resources\Projects\Pages\CreateProject;
use MikePageDev\FilamentPortfolio\Filament\Resources\Projects\Pages\EditProject;
use MikePageDev\FilamentPortfolio\Filament\Resources\Projects\Pages\ListProjects;
use MikePageDev\FilamentPortfolio\Models\Project;
use MikePageDev\FilamentPortfolio\Tests\Fixtures\User;
use PHPUnit\Framework\Attributes\DataProvider;

class ProjectResourceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->actingAs(User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'secret-password',
        ]));
    }

    /**
     * @return array<string, mixed>
     */
    private function validData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Demo App',
            'slug' => 'demo-app',
            'summary' => 'Demo summary.',
            'problem' => 'Demo problem.',
            'role' => 'Demo role.',
            'tech' => ['Laravel', 'Filament'],
            'case_study' => [['heading' => 'Approach', 'body' => 'Approach body.']],
            'images' => [],
            'live_url' => 'https://demo.example.test',
            'source_url' => 'https://github.com/example/demo',
            'is_published' => true,
        ], $overrides);
    }

    public function test_list_page_shows_projects_in_sort_order(): void
    {
        $second = Project::factory()->create(['sort_order' => 2]);
        $first = Project::factory()->create(['sort_order' => 1]);

        Livewire::test(ListProjects::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$first, $second], inOrder: true);
    }

    public function test_can_create_a_project_with_a_screenshot(): void
    {
        $undoRepeaterFake = Repeater::fake();

        Livewire::test(CreateProject::class)
            ->fillForm($this->validData([
                'images' => [['path' => [UploadedFile::fake()->image('home.png')], 'alt' => 'Home page']],
            ]))
            ->call('create')
            ->assertHasNoFormErrors();

        $undoRepeaterFake();

        $project = Project::query()->sole();
        $this->assertSame('demo-app', $project->slug);
        $this->assertTrue($project->is_published);
        $this->assertSame(['Laravel', 'Filament'], $project->tech);
        $this->assertSame('Home page', $project->images[0]['alt'] ?? null);
        Storage::disk('public')->assertExists($project->images[0]['path'] ?? '');
        $this->assertStringStartsWith('portfolio/', $project->images[0]['path'] ?? '');
    }

    public function test_slug_is_generated_from_the_title_on_create(): void
    {
        Livewire::test(CreateProject::class)
            ->fillForm(['title' => 'Café & Co.'])
            ->assertFormSet(['slug' => 'cafe-co']);
    }

    /**
     * @return array<string, array{array<string, mixed>, string, string}>
     */
    public static function invalidData(): array
    {
        return [
            'missing title' => [['title' => ''], 'title', 'required'],
            'title too long' => [['title' => str_repeat('a', 151)], 'title', 'max'],
            'slug with uppercase' => [['slug' => 'Demo-App'], 'slug', 'regex'],
            'slug with a dot' => [['slug' => 'demo.app'], 'slug', 'regex'],
            'http live url' => [['live_url' => 'http://demo.example.test'], 'live_url', 'starts_with'],
            'not a url' => [['source_url' => 'not a url'], 'source_url', 'url'],
        ];
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    #[DataProvider('invalidData')]
    public function test_invalid_data_is_rejected(array $overrides, string $field, string $rule): void
    {
        Livewire::test(CreateProject::class)
            ->fillForm($this->validData($overrides))
            ->call('create')
            ->assertHasFormErrors([$field => $rule]);

        $this->assertSame(0, Project::query()->count());
    }

    public function test_duplicate_slug_is_rejected(): void
    {
        Project::factory()->create(['slug' => 'demo-app']);

        Livewire::test(CreateProject::class)
            ->fillForm($this->validData())
            ->call('create')
            ->assertHasFormErrors(['slug' => 'unique']);
    }

    public function test_screenshot_alt_text_is_required(): void
    {
        $undoRepeaterFake = Repeater::fake();

        Livewire::test(CreateProject::class)
            ->fillForm($this->validData([
                'images' => [['path' => [UploadedFile::fake()->image('home.png')], 'alt' => '']],
            ]))
            ->call('create')
            ->assertHasFormErrors(['images.0.alt' => 'required']);

        $undoRepeaterFake();
    }

    public function test_svg_screenshots_are_rejected(): void
    {
        $undoRepeaterFake = Repeater::fake();

        $svg = UploadedFile::fake()->createWithContent(
            'evil.svg',
            '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>',
        );

        Livewire::test(CreateProject::class)
            ->fillForm($this->validData([
                'images' => [['path' => [$svg], 'alt' => 'Evil']],
            ]))
            ->call('create')
            ->assertHasFormErrors(['images.0.path']);

        $undoRepeaterFake();

        $this->assertSame(0, Project::query()->count());
    }

    public function test_editing_without_changing_the_slug_saves(): void
    {
        $project = Project::factory()->create(['slug' => 'demo-app', 'title' => 'Demo App']);

        Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])
            ->fillForm(['summary' => 'Updated summary.'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Updated summary.', $project->fresh()?->summary);
    }

    public function test_reordering_updates_sort_order(): void
    {
        $a = Project::factory()->create(['sort_order' => 1]);
        $b = Project::factory()->create(['sort_order' => 2]);

        Livewire::test(ListProjects::class)
            ->call('reorderTable', [(string) $b->getKey(), (string) $a->getKey()]);

        $this->assertSame([$b->id, $a->id], Project::query()->ordered()->pluck('id')->all());
    }

    public function test_publish_toggle_in_the_table_updates_the_project(): void
    {
        $project = Project::factory()->create();

        Livewire::test(ListProjects::class)
            ->call('updateTableColumnState', 'is_published', (string) $project->getKey(), true);

        $this->assertTrue($project->fresh()?->is_published);
    }
}
