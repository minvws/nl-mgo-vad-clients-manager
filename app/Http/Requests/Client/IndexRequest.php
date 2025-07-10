<?php

declare(strict_types=1);

namespace App\Http\Requests\Client;

use App\Http\Dtos\Client\IndexRequestDto;
use App\Http\Requests\TypedRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use TypeError;

class IndexRequest extends TypedRequest
{
    /** @var array<string> */
    private array $allowedSortOptions = [
        'clients.created_at',
        'clients.updated_at',
        'clients.fqdn',
        'organisations.name',
        'organisations.main_contact_email',
    ];

    /**
     * @return array<string, array<int, In|string>>
     */
    public function rules(): array
    {
        return [
            'search' => [
                'string',
                'nullable',
                'sometimes',
                'min:1',
                'max:255',
            ],
            'sort' => [
                'sometimes',
                'string',
                Rule::in($this->allowedSortOptions),
            ],
            'direction' => [
                'sometimes',
                'string',
                Rule::in(['asc', 'desc']),
            ],
            'active' => [
                'sometimes',
                'boolean',
                'nullable',
            ],
        ];
    }

    /**
     * @throws TypeError
     */
    public function getValidatedDto(): IndexRequestDto
    {
        $validated = $this->safe();
        //make sure we have a default sort and direction
        $validated['sort'] = $validated['sort'] ?? 'clients.created_at';
        $validated['direction'] = $validated['direction'] ?? 'desc';

        return new IndexRequestDto($validated);
    }
}
