<?php


namespace Database\Factories;

use App\Enums\ProductType;
use App\Enums\ProductStatusEnum;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(3);
        return [
            'title'       => $title,
            'slug'        => Str::slug($title).'-'.Str::random(6),
            'description' => $this->faker->paragraph(),
            'price'       => $this->faker->numberBetween(1000, 99900) / 100, // RM
            'quantity'    => $this->faker->numberBetween(0, 50),
            'status'      => ProductStatusEnum::Published,
            'type'        => ProductType::Physical, // default
            'metadata'    => [],
            'created_by'  => \App\Models\User::factory(),
            'category_id' => \App\Models\Category::factory(),
            'department_id'=> \App\Models\Department::factory(),
        ];
    }
}
