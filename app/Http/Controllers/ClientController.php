<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use App\Http\Requests\Client\CreateRequest;
use App\Http\Requests\Client\UpdateRequest;
use App\Models\Client;
use App\Models\Organisation;
use App\Support\I18n;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

use function is_string;
use function redirect;
use function route;
use function view;

class ClientController extends Controller
{
    public const int CLIENT_PAGINATION_SIZE = 25;

    public function index(Request $request): View
    {
        $queryBuilder = Client::query()
            ->select([
                'clients.*',
                'organisations.id as organisation_id',
                'organisations.name',
                'organisations.main_contact_email',
            ])
            ->join('organisations', 'clients.organisation_id', '=', 'organisations.id')
            ->orderBy($this->getRequestSort($request), $this->getRequestDirection($request));

        $searchQuery = $this->getRequestSearch($request);
        if ($searchQuery !== '') {
            $queryBuilder = $queryBuilder->where(
                static fn (Builder $query) => $query->where('clients.id', 'ilike', '%' . $searchQuery . '%')
                    ->orWhere('clients.fqdn', 'ilike', '%' . $searchQuery . '%')
                    ->orWhereRelation('organisation', 'name', 'ilike', '%' . $searchQuery . '%')
                    ->orWhereRelation('organisation', 'main_contact_email', 'ilike', '%' . $searchQuery . '%'),
            );
        }

        $paginator = $queryBuilder->paginate(self::CLIENT_PAGINATION_SIZE);
        if ($paginator instanceof LengthAwarePaginator) {
            $paginator->withQueryString();
        }

        return view('clients.index')
            ->with('clients', $paginator)
            ->with('search', $this->getRequestSearch($request))
            ->with('sort', $this->getRequestSort($request))
            ->with('direction', $this->getRequestDirection($request));
    }

    private function getRequestSort(Request $request): string
    {
        $sort = $request->query('sort');

        return is_string($sort) ? $sort : 'clients.created_at';
    }

    private function getRequestDirection(Request $request): string
    {
        $direction = $request->query('direction');

        return is_string($direction) ? $direction : 'desc';
    }

    private function getRequestSearch(Request $request): string
    {
        $search = $request->input('search');

        return is_string($search) ? $search : '';
    }

    public function create(): View
    {
        $organisations = Organisation::all();

        return view('clients.create')->with('organisations', $organisations);
    }

    public function store(CreateRequest $request): RedirectResponse
    {
        Client::query()->create($request->getValidatedAttributes());

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

    public function update(UpdateRequest $request, Client $client): RedirectResponse
    {
        $client->update($request->getValidatedAttributes());

        return redirect(route('clients.index'))
            ->with(
                'flash_notification',
                new FlashNotification(FlashNotificationTypeEnum::CONFIRMATION, I18n::trans('client.updated_successfully')),
            );
    }
}
