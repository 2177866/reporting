<?php

namespace Alyakin\Reporting\Database\Factories;

use Alyakin\Reporting\Models\Report;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition()
    {
        return [
            'reason' => $this->faker->sentence,
            'meta' => ['severity' => $this->faker->randomElement(['low', 'medium', 'high'])],
            'user_id' => null,
        ];
    }
}
