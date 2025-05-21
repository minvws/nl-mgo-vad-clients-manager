<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Middleware;

use App\Actions\Fortify\EnsureUserIsActive;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Translation\Translator;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class EnsureUserIsActiveTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $translatorMock = Mockery::mock(Translator::class);
        $translatorMock->shouldReceive('get')
            ->with('authentication.inactive', [], null)
            ->andReturnArg(0);

        App::instance('translator', $translatorMock);
    }

    public function testHandleThrowsValidationExceptionForInactiveUser(): void
    {
        $inactiveUser = User::factory()->create(['active' => false]);
        $request = Mockery::mock(Request::class);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($inactiveUser);

        $middleware = new EnsureUserIsActive();

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('authentication.inactive');

        $middleware->handle($request, function (): void {
            $this->fail('Next middleware should not be called');
        });
    }

    /**
     * @throws ValidationException
     */
    public function testHandlePassesForActiveUser(): void
    {
        $activeUser = User::factory()->create(['active' => true]);
        $request = Mockery::mock(Request::class);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($activeUser);

        $middleware = new EnsureUserIsActive();

        $result = $middleware->handle($request, function () {
            return 'next called';
        });

        $this->assertEquals('next called', $result);
    }

    /**
     * @throws ValidationException
     */
    public function testHandlePassesForGuestUser(): void
    {
        $request = Mockery::mock(Request::class);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn(null);

        $middleware = new EnsureUserIsActive();

        $result = $middleware->handle($request, function () {
            return 'next called';
        });

        $this->assertEquals('next called', $result);
    }
}
