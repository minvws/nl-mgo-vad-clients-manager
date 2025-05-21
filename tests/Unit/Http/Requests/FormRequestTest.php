<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Mockery;
use Tests\TestCase;

use function fake;

class FormRequestTest extends TestCase
{
    public function testGetValidatedAttributesReturnsTypedArray(): void
    {
        $validatedAttributes = [
            fake()->word() => fake()->word(),
            fake()->word() => fake()->numerify('##'),
        ];
        $mockValidator = Mockery::mock(Validator::class);
        $formRequest = new FormRequest();
        $formRequest->setValidator($mockValidator);

        $mockValidator->expects('validated')
            ->once()
            ->andReturns($validatedAttributes);

        $this->assertSame(
            $validatedAttributes,
            $formRequest->getValidatedAttributes(),
        );
    }
}
