<?php

namespace Database\Factories;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asset>
 */
class AssetFactory extends Factory
{
    protected $model = Asset::class;

    protected static array $categories = [
        'Laptop', 'Monitor', 'Phone', 'Vehicle', 'Furniture', 'Networking Equipment',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $category = fake()->randomElement(static::$categories);

        return [
            'name' => $category.' '.fake()->bothify('#??').' '.fake()->word(),
            'category' => $category,
            'status' => fake()->randomElement(['active', 'active', 'active', 'maintenance', 'retired']),
            'value' => fake()->randomFloat(2, 50, 5000),
            'assigned_user_id' => null,
        ];
    }
}
