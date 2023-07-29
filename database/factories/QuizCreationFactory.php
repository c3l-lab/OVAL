<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\oval\Models\Model>
 */
class QuizCreationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "media_type" => "youtube",
            "quiz_data" => json_encode('[
                {
                    "name": "Quiz @ 8 seconds",
                    "stop": "8",
                    "items": [
                        {
                            "ans": ["A:4342"],
                            "list": ["A:4342"],
                            "type": "multiple_choice",
                            "title": "4434",
                            "feedback": [""]
                        }
                    ]
                }
            ]'),
            "visable" => 1,
        ];
    }
}
