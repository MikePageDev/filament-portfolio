<?php

namespace MikePageDev\FilamentPortfolio\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MikePageDev\FilamentPortfolio\Models\Project;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'title' => ucfirst($title),
            'slug' => str($title)->slug()->toString(),
            'summary' => fake()->sentence(),
            'is_published' => false,
            'sort_order' => 0,
        ];
    }

    public function published(): static
    {
        return $this->state(['is_published' => true]);
    }
}
