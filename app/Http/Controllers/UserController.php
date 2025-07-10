<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use App\Enums\Role;
use App\Events\Logging\CreateUserEvent;
use App\Events\Logging\DeleteUserEvent;
use App\Events\Logging\UpdateUserEvent;
use App\Events\Logging\ViewUserEvent;
use App\Http\Requests\User\CreateRequest;
use App\Http\Requests\User\FilterRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\User;
use App\Services\UserService;
use App\Support\Auth;
use App\Support\I18n;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use MinVWS\Logging\Laravel\LogService;
use TypeError;
use ValueError;
use Webmozart\Assert\Assert;

use function array_map;
use function redirect;
use function view;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected readonly LogService $logger,
    ) {
    }

    /**
     * @throws AuthorizationException
     * @throws TypeError
     */
    public function index(FilterRequest $request): View
    {
        Gate::authorize('index', User::class);
        $dto = $request->getValidatedDto();
        $users = User::query()
            ->scopes(['filterByNameOrEmail' => $dto->filter])
            ->when(
                value: $dto->sort,
                callback: static fn(Builder $query) => $query->orderBy(
                    column: $dto->sort,
                    direction: $dto->direction,
                ),
            )
            ->paginate();

        return view('users.index', [
            'users' => $users,
            'currentUser' => Auth::user(),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function create(): View
    {
        Gate::authorize('create', User::class);

        $availableRoles = Role::cases();

        return view('users.create', [
            'roles' => $availableRoles,
        ]);
    }

    /**
     * @throws TypeError
     * @throws ValueError
     */
    public function store(CreateRequest $request): RedirectResponse
    {
        $dto = $request->getValidatedDto();
        $roles = $dto->roles;

        Assert::isArray($roles);
        Assert::allString($roles);

        $user = $this->userService->createUser(
            name: $dto->name,
            email: $dto->email,
            roles: array_map(
                static fn(mixed $role): Role => Role::from($role),
                $roles,
            ),
        );

        $this->logger->log((new CreateUserEvent())
            ->withActor(Auth::user())
            ->withData([
                'userId' => $user->id,
            ]));

        return redirect()
            ->route('users.index')
            ->with(FlashNotification::SESSION_KEY, new FlashNotification(
                type: FlashNotificationTypeEnum::CONFIRMATION,
                message: I18n::trans('user.flash.created'),
            ));
    }

    /**
     * @throws AuthorizationException
     */
    public function edit(User $user): View
    {
        Gate::authorize('edit', [User::class, $user]);

        $this->logger->log((new ViewUserEvent())
            ->withActor(Auth::user())
            ->withData([
                'userId' => $user->id,
            ]));

        return view('users.edit', [
            'user' => $user,
            'availableRoles' => Role::cases(),
        ]);
    }

    /**
     * @throws TypeError
     */
    public function update(UpdateRequest $request, User $user): RedirectResponse
    {
        $this->logger->log((new UpdateUserEvent())
            ->withActor(Auth::user())
            ->withData([
                'userId' => $user->id,
            ]));

        $this->userService->updateUser($request, $user);

        return Redirect::route('users.index')
            ->with(FlashNotification::SESSION_KEY, new FlashNotification(
                type: FlashNotificationTypeEnum::CONFIRMATION,
                message: I18n::trans('user.flash.updated'),
            ));
    }

    /**
     * @throws AuthorizationException
     */
    public function remove(User $user): View
    {
        Gate::authorize('delete', [User::class, $user]);

        return view('users.delete', [
            'user' => $user,
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function delete(User $user): RedirectResponse
    {
        Gate::authorize('delete', [User::class, $user]);

        $this->logger->log((new DeleteUserEvent())
            ->withActor(Auth::user())
            ->withData([
                'userId' => $user->id,
            ]));

        $this->userService->deleteUser($user);

        return Redirect::route('users.index')
            ->with(FlashNotification::SESSION_KEY, new FlashNotification(
                type: FlashNotificationTypeEnum::CONFIRMATION,
                message: I18n::trans('user.flash.deleted'),
            ));
    }

    /**
     * @throws AuthorizationException
     */
    public function reset(User $user): View
    {
        Gate::authorize('reset', [User::class, $user]);

        return view('users.reset', [
            'user' => $user,
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function doReset(User $user): RedirectResponse
    {
        Gate::authorize('reset', [User::class, $user]);

        $this->userService->resetUser($user);

        return Redirect::route('users.index')
            ->with(FlashNotification::SESSION_KEY, new FlashNotification(
                type: FlashNotificationTypeEnum::CONFIRMATION,
                message: I18n::trans('user.flash.reset_message'),
            ));
    }

    public function deactivate(User $user): RedirectResponse
    {
        Gate::authorize('changeActiveStatus', [User::class, $user]);

        $user->update([
            'active' => false,
        ]);

        return Redirect::route('users.index');
    }

    public function activate(User $user): RedirectResponse
    {
        Gate::authorize('changeActiveStatus', [User::class, $user]);

        $user->update([
            'active' => true,
        ]);


        return Redirect::route('users.index');
    }
}
