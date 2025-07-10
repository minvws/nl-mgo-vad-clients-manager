<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Requests\RegistrationRequest;

use App\Http\Requests\RegistrationRequest\CreateRequest;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use Tests\Feature\Http\Requests\CocNumberValidationProvider;
use Tests\TestCase;

class CreateRequestTest extends TestCase
{
    #[DataProviderExternal(CocNumberValidationProvider::class, 'validationProvider')]
    public function testCocNumberValidationCreateRequest(mixed $data, bool $expected): void
    {
        $rules = [];
        $rules['organisation_coc_number'] = (new CreateRequest())->rules()['organisation_coc_number'];

        $validator = Validator::make(['organisation_coc_number' => $data], $rules);
        $this->assertSame($expected, $validator->passes());
    }
}
