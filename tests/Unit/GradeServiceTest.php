<?php

namespace Tests\Unit;

use App\Services\GradeService;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class GradeServiceTest extends TestCase
{
    public function test_calculates_final_score_and_rejects_values_above_component_limits(): void
    {
        $service = new GradeService;
        $components = ['ser' => 10, 'saber' => 45, 'hacer' => 40, 'autoevaluacion' => 5];

        $this->assertSame(100.0, $service->finalScore($service->validateComponents($components)));

        $this->expectException(ValidationException::class);
        $service->validateComponents([...$components, 'ser' => 11]);
    }

    public function test_rejects_precision_that_the_database_cannot_store(): void
    {
        $this->expectException(ValidationException::class);

        (new GradeService)->validateComponents([
            'ser' => 5.25,
            'saber' => 20,
            'hacer' => 20,
            'autoevaluacion' => 3,
        ]);
    }
}
