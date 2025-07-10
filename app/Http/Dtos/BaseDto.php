<?php

declare(strict_types=1);

namespace App\Http\Dtos;

use App\Exception\CouldNotResolveBooleanRepresentation;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\ValidatedInput;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use TypeError;

use function array_key_exists;
use function gettype;
use function is_array;
use function is_bool;
use function is_int;
use function is_numeric;
use function is_object;
use function is_string;
use function sprintf;
use function strtolower;

/**
 * @implements Arrayable<string, bool|int|string|array|object|null>
 * @SuppressWarnings("PHPMD.NumberOfChildren")
 */
abstract readonly class BaseDto implements Arrayable
{
    /**
     * @param ValidatedInput | array<string, bool|int|string|object|null> $data
     *
     * @throws TypeError
     */
    public function __construct(ValidatedInput|array $data = [])
    {
        if ($data instanceof ValidatedInput) {
            $data = $data->toArray();
        }

        foreach ($this->getPublicProperties() as $property) {
            $name = $property->getName();
            $type = $property->getType();

            if (!$type instanceof ReflectionNamedType) {
                throw new TypeError(
                    sprintf(
                        'Only named types are currently supported on DTOs that extend %s; found non-named type on property %s in %s',
                        self::class,
                        $property->getName(),
                        $this::class,
                    ),
                );
            }

            $value = $this->resolveValue($data, $name, $type);

            $valueSetterClosure = $this->bindClosure($name);
            $valueSetterClosure($value);
        }
    }

    /**
     * @return array<ReflectionProperty>
     */
    private function getPublicProperties(): array
    {
        return (new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC);
    }

    /**
     * @param array<string, bool|int|object|string|null> $data
     *
     * @throws TypeError
     */
    private function resolveValue(array $data, string $name, ReflectionNamedType $type): mixed
    {
        if (!array_key_exists($name, $data)) {
            return $this->handleMissingProperty($name, $type);
        }

        $value = $data[$name];
        $typeName = $type->getName();

        if ($value === null) {
            return $value;
        }

        $value = $this->processValueByType($value, $typeName, $name);
        $this->validateValueType($value, $typeName, $name);

        return $value;
    }

    private function handleMissingProperty(string $name, ReflectionNamedType $type): mixed
    {
        if ($type->allowsNull()) {
            return null;
        }

        throw new InvalidArgumentException(
            sprintf('Missing DTO property: %s', $name),
        );
    }

    /**
     * @throws TypeError
     */
    private function processValueByType(mixed $value, string $typeName, string $name): mixed
    {
        if ($typeName === 'bool' && !$this->isValidType($value, $typeName) && (is_string($value) || is_int($value))) {
            try {
                return $this->resolveBooleanRepresentation($value);
            } catch (CouldNotResolveBooleanRepresentation) {
                $this->throwTypeError($name, $typeName, $value);
            }
        }

        if ($typeName === 'int' && is_string($value) && is_numeric($value)) {
            return (int) $value;
        }

        return $value;
    }

    /**
     * @throws TypeError
     */
    private function validateValueType(mixed $value, string $typeName, string $name): void
    {
        if (!$this->isValidType($value, $typeName)) {
            $this->throwTypeError($name, $typeName, $value);
        }
    }

    private function resolveBooleanRepresentation(int|string $booleanRepresentation): bool
    {
        if (is_string($booleanRepresentation)) {
            return match (strtolower($booleanRepresentation)) {
                'true' => true,
                'false' => false,
                default => (bool) $booleanRepresentation,
            };
        }

        if (is_int($booleanRepresentation) && ($booleanRepresentation === 0 || $booleanRepresentation === 1)) {
            return (bool) $booleanRepresentation;
        }

        throw new CouldNotResolveBooleanRepresentation(
            sprintf(
                'Could not resolve boolean representation: %s',
                (string) $booleanRepresentation,
            ),
        );
    }

    /**
     * @throws TypeError
     */
    private function throwTypeError(string $name, string $typeName, mixed $value): void
    {
        $className = $this::class;
        $valueType = is_object($value) ? $value::class : gettype($value);
        throw new TypeError(sprintf(
            'Property %s on %s must be of type %s, but %s given',
            $name,
            $className,
            $typeName,
            $valueType,
        ));
    }

    private function isValidType(mixed $value, string $type): bool
    {
        return match ($type) {
            'int' => is_int($value),
            'string' => is_string($value),
            'bool' => is_bool($value),
            'array' => is_array($value),
            default => $value instanceof $type,
        };
    }

    private function bindClosure(string $property): Closure
    {
        $setter = function ($value) use ($property): void {
            $this->{$property} = $value;
        };

        return Closure::bind($setter, $this, $this::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $array = [];
        foreach ($this->getPublicProperties() as $property) {
            $name = $property->getName();
            $array[$name] = $this->{$name};
        }

        return $array;
    }
}
