<?php

namespace Database\Factories;

use App\Models\Livro;
use App\Models\Editora;
use Illuminate\Database\Eloquent\Factories\Factory;

class LivroFactory extends Factory
{
    protected $model = Livro::class;

    public function definition(): array
    {
        return [
            'isbn' => $this->faker->unique()->isbn13(),
            'nome' => $this->faker->sentence(3),
            'editora_id' => Editora::factory(),
            'bibliografia' => $this->faker->paragraph(),
            'imagem_capa' => $this->faker->imageUrl(),
            'preco' => $this->faker->randomFloat(2, 5, 50),
            'stock' => 1,
        ];
    }

    public function semStock(): self
    {
        return $this->state(fn () => ['stock' => 0]);
    }

    public function comStock(int $stock): self
    {
        return $this->state(fn () => ['stock' => $stock]);
    }
}
