<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use App\Http\Requests\Organisation\CreateRequest;
use App\Http\Requests\Organisation\UpdateRequest;
use App\Models\Organisation;
use App\Support\I18n;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use TypeError;

use function redirect;
use function route;
use function view;

class OrganisationController extends Controller
{
    public function index(): View
    {
        $organisations = Organisation::all();
        return view('organisations.index')->with('organisations', $organisations);
    }

    public function create(): View
    {
        return view('organisations.create');
    }

    /**
     * @throws TypeError
     */
    public function store(CreateRequest $request): RedirectResponse
    {
        $dto = $request->getValidatedDto();

        Organisation::query()->create([
            'name' => $dto->name,
            'main_contact_email' => $dto->main_contact_email,
            'main_contact_name' => $dto->main_contact_name,
            'coc_number' => $dto->coc_number,
            'notes' => $dto->notes,
        ]);

        return redirect(route('organisations.index'))->with(
            'flash_notification',
            new FlashNotification(FlashNotificationTypeEnum::CONFIRMATION, I18n::trans('organisation.created_successfully')),
        );
    }

    public function edit(Organisation $organisation): View
    {
        return view('organisations.edit')->with('organisation', $organisation);
    }

    /**
     * @throws TypeError
     */
    public function update(UpdateRequest $request, Organisation $organisation): RedirectResponse
    {
        $dto = $request->getValidatedDto();
        $organisation->update([
            'name' => $dto->name,
            'main_contact_email' => $dto->main_contact_email,
            'main_contact_name' => $dto->main_contact_name,
            'coc_number' => $dto->coc_number,
            'notes' => $dto->notes,
        ]);

        return redirect(route('organisations.index'))->with(
            'flash_notification',
            new FlashNotification(FlashNotificationTypeEnum::CONFIRMATION, I18n::trans('organisation.updated_successfully')),
        );
    }
}
