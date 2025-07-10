<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Dtos;

use App\Http\Dtos\BaseDto;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use TypeError;

use function gettype;
use function sprintf;

// phpcs:ignore
readonly class TestDto extends BaseDto
{
    public int $id;
    public string $name;
    public bool $is_active;
}

// phpcs:ignore
readonly class TestDtoWithUnionTypes extends BaseDto
{
    public int|string $id;
}


class BaseDtoTest extends TestCase //phpcs:ignore
{
    public function testBaseDtoMapsArrayToDto(): void
    {
        $data = [
            'id' => 1,
            'name' => 'Test Name',
            'is_active' => true,
        ];

        $dto = new TestDto($data);

        $this->assertEquals($data['id'], $dto->id);
        $this->assertEquals($data['name'], $dto->name);
    }

    public function testBaseDtoThrowsExceptionOnMissingData(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing DTO property: id');

        new TestDto(['name' => 'Test Name', 'is_active' => true]);
    }

    public function testBaseDtoThrowsExceptionOnInvalidDataType(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('Property id on Tests\Feature\Http\Dtos\TestDto must be of type int, but string given');

        new TestDto(['id' => 'invalid', 'name' => 'Test Name']);
    }

    #[DataProvider('booleanRepresentationProvider')]
    public function testBaseDtoCanTransformBooleanRepresentationToBoolean(mixed $input, bool $expected): void
    {
        $data = [
            'id' => 1,
            'name' => 'Test Name',
            'is_active' => $input,
        ];

        $dto = new TestDto($data);
        $this->assertSame($expected, $dto->is_active);
    }

    public static function booleanRepresentationProvider(): array
    {
        return [
            'true string' => ['true', true],
            'false string' => ['false', false],
            '1 int' => [1, true],
            '0 int' => [0, false],
            'true bool' => [true, true],
            'false bool' => [false, false],
            'TRUE uppercase' => ['TRUE', true],
            'FALSE uppercase' => ['FALSE', false],
            'random string' => ['yes', true],
            'empty string' => ['', false],
        ];
    }

    #[DataProvider('integerRepresentationProvider')]
    public function testBaseDtoCanTransformIntegerRepresentationToBoolean(
        string|int $input,
        int $expected,
    ): void {
        $data = [
            'id' => $input,
            'name' => 'Test Name',
            'is_active' => 'true',
        ];

        $dto = new TestDto($data);
        $this->assertSame($expected, $dto->id);
    }

    public static function integerRepresentationProvider(): array
    {
        return [
            '1 int' => [1, 1],
            '0 int' => [0, 0],
            '1 string' => ['1', 1],
            '0 string' => ['0', 0],
            '123 int' => [123, 123],
            '123 string' => ['123', 123],
        ];
    }

    public function testBaseDtoIsArrayAble(): void
    {
        $data = [
            'id' => 1,
            'name' => 'Test Name',
            'is_active' => true,
        ];

        $dto = new TestDto($data);

        $this->assertEquals($data, $dto->toArray());
    }

    public function testBaseDTODoesNotAllowUnionTypes(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            'Only named types are currently supported on DTOs that extend App\Http\Dtos\BaseDto; found non-named type on property id in Tests\Feature\Http\Dtos\TestDtoWithUnionTypes',
        );

        new TestDtoWithUnionTypes(['id' => 'invalid']);
    }

    public function testItThrowsTypeErrorWhenEncounteringInvalidBooleanRepresentation(): void
    {
        $booleanRepresentation = 3;
        $data = [
            'id' => 1,
            'name' => 'Test Name',
            'is_active' => $booleanRepresentation,
        ];

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            sprintf(
                'Property is_active on Tests\Feature\Http\Dtos\TestDto must be of type bool, but %s given',
                gettype($booleanRepresentation),
            ),
        );

        new TestDto($data);
    }
}
