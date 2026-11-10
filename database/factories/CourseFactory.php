<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'thumbnail' => 'default.jpg',
            'level' => $this->faker->randomElement(['beginner', 'intermediate', 'advanced']),
            // By default assign a tutor; can be overridden in tests.
            'tutor_id' => User::factory()->state(['role' => 'tutor'])->create()->id,
        ];
    }
}
