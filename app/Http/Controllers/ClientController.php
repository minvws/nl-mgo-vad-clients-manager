<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use App\Http\Requests\Client\CreateRequest;
use App\Http\Requests\Client\IndexRequest;
use App\Http\Requests\Client\UpdateRequest;
use App\Models\Client;
use App\Models\Organisation;
use App\Support\I18n;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use TypeError;

use function redirect;
use function route;
use function view;

class ClientController extends Controller
{
    public const int CLIENT_PAGINATION_SIZE = 25;

    /**
     * @throws TypeError
     */
    public function index(IndexRequest $request): View
    {
        $dto = $request->getValidatedDto();
        $queryBuilder = Client::query()
            ->select([
                'clients.*',
                'organisations.id as organisation_id',
                'organisations.name',
                'organisations.main_contact_email',
            ])
            ->join('organisations', 'clients.organisation_id', '=', 'organisations.id')
            ->orderBy($dto->sort, $dto->direction);

        if ($dto->search !== null) {
            $queryBuilder = $queryBuilder->where(
                static fn(Builder $query) => $query->where('clients.id', 'ilike', '%' . $dto->search . '%')
                    ->orWhere('clients.fqdn', 'ilike', '%' . $dto->search . '%')
                    ->orWhereRelation('organisation', 'name', 'ilike', '%' . $dto->search . '%')
                    ->orWhereRelation('organisation', 'main_contact_email', 'ilike', '%' . $dto->search . '%'),
            );
        }

        if ($dto->active !== null) {
            $queryBuilder->where('clients.active', $dto->active);
        }

        $paginator = $queryBuilder->paginate(self::CLIENT_PAGINATION_SIZE);
        if ($paginator instanceof LengthAwarePaginator) {
            $paginator->withQueryString();
        }

        return view('clients.index')
            ->with('clients', $paginator)
            ->with('search', $dto->search)
            ->with('sort', $dto->sort)
            ->with('direction', $dto->direction)
            ->with('active', $dto->active);
    }

    public function create(): View
    {
        $organisations = Organisation::all();

        return view('clients.create')->with('organisations', $organisations);
    }

    /**
     * @throws TypeError
     */
    public function store(CreateRequest $request): RedirectResponse
    {
        $dto = $request->getValidatedDto();
        Client::query()->create(
            [
                'organisation_id' => $dto->organisation_id,
                'redirect_uris' => $dto->redirect_uris,
                'fqdn' => $dto->fqdn,
                'active' => $dto->active,
            ],
        );

        return redirect(route('clients.index'))->with(
            'flash_notification',
            new FlashNotification(FlashNotificationTypeEnum::CONFIRMATION, I18n::trans('client.created_successfully')),
        );
    }

    public function edit(Client $client): View
    {
        return view('clients.edit')
            ->with('client', $client)
            ->with('organisations', Organisation::query()->select(['id', 'name'])->orderBy('name', 'asc')->get());
    }

    /**
     * @throws TypeError
     */
    public function update(UpdateRequest $request, Client $client): RedirectResponse
    {
        $dto = $request->getValidatedDto();
        $client->update([
            'organisation_id' => $dto->organisation_id,
            'redirect_uris' => $dto->redirect_uris,
            'fqdn' => $dto->fqdn,
            'active' => $dto->active,
        ]);

        return redirect(route('clients.index'))
            ->with(
                'flash_notification',
                new FlashNotification(FlashNotificationTypeEnum::CONFIRMATION, I18n::trans('client.updated_successfully')),
            );
    }
}
