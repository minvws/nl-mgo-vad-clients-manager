<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Requests\Organisation;

use App\Http\Requests\Organisation\CreateRequest;
use App\Http\Requests\Organisation\UpdateRequest;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use Tests\Feature\Http\Requests\CocNumberValidationProvider;
use Tests\TestCase;

class RequestTest extends TestCase
{
    #[DataProviderExternal(CocNumberValidationProvider::class, 'validationProvider')]
    public function testCocNumberValidationCreateRequest(mixed $data, bool $expected): void
    {
        $rules = [];
        $rules['coc_number'] = (new CreateRequest())->rules()['coc_number'];

        $validator = Validator::make(['coc_number' => $data], $rules);
        $this->assertSame($expected, $validator->passes());
    }

    #[DataProviderExternal(CocNumberValidationProvider::class, 'validationProvider')]
    public function testCocNumberValidationUpdateRequest(mixed $data, bool $expected): void
    {
        $rules = [];
        $rules['coc_number'] = (new UpdateRequest())->rules()['coc_number'];

        $validator = Validator::make(['coc_number' => $data], $rules);
        $this->assertSame($expected, $validator->passes());
    }
}
