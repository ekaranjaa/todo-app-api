<?php

namespace Database\Factories;

use App\Models\Todo;
use Illuminate\Database\Eloquent\Factories\Factory;

class TodoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Todo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->text(40),
            'memo' => $this->faker->text(100),
            'completed' => $this->faker->boolean(),
            'pinned' => $this->faker->boolean(20),
            'created_at' => $this->faker->dateTimeBetween('-60 days')
        ];
    }
}
