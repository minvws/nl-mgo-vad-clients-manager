<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Http\Dtos\BaseDto;
use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use TypeError;

/**
 * @SuppressWarnings("PHPMD.NumberOfChildren")
 */
abstract class TypedRequest extends FormRequest
{
    /**
     * @return array<string, string | Rule | Closure | array<int, string|Rule|Closure>>
     */
    abstract public function rules(): array;

    /**
     * @throws TypeError
     */
    abstract public function getValidatedDto(): BaseDto;
}
