<?php

namespace Database\Factories;

use App\Models\Visitor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Visitor>
 */
class VisitorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->name();
        $email = $this->faker->unique()->safeEmail();
        $phone = $this->faker->phoneNumber();

        return [
            'qr_raw_data' => json_encode([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
            ]),
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'scanned_at' => now(),
        ];
    }

    /**
     * Indicate that the QR code contains plain text.
     */
    public function plainText(): static
    {
        return $this->state(function (array $attributes): array {
            return [
                'qr_raw_data' => 'Plain visitor token: ' . $this->faker->uuid(),
                'name' => null,
                'email' => null,
                'phone' => null,
            ];
        });
    }
}
