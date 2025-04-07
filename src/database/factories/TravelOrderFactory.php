<?php

namespace Database\Factories;

use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TravelOrder>
 */
class TravelOrderFactory extends Factory
{
    protected $model = TravelOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'requester_name' => $this->faker->name(),
            'destination'    => $this->faker->city(),
            'start_date'     => $this->faker->dateTimeBetween('+1 week', '+2 weeks'),
            'end_date'       => $this->faker->dateTimeBetween('+2 weeks', '+3 weeks'),
            'status'         => 'solicitado',
            'user_id'        => User::factory(),
        ];
    }
}
