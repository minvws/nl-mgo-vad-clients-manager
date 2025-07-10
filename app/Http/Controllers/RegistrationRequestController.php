<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\RegistrationRequest\CreateRequest;
use App\Models\RegistrationRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use TypeError;

use function redirect;
use function view;

class RegistrationRequestController extends Controller
{
    public function create(): View
    {
        return view('registration-requests.create');
    }

    /**
     * @throws TypeError
     */
    public function store(CreateRequest $request): RedirectResponse
    {
        $dto = $request->getValidatedDto();
        RegistrationRequest::create(
            [
                'organisation_name' => $dto->organisation_name,
                'organisation_main_contact_email' => $dto->organisation_main_contact_email,
                'organisation_main_contact_name' => $dto->organisation_main_contact_name,
                'organisation_coc_number' => $dto->organisation_coc_number,
                'client_fqdn' => $dto->client_fqdn,
                'client_redirect_uris' => $dto->client_redirect_uris,
            ],
        );

        return redirect()->route('registration-requests.thank-you');
    }

    public function thankYou(): View
    {
        return view('registration-requests.thank-you');
    }
}
