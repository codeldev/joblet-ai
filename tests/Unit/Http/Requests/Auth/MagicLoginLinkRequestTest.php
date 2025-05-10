<?php

/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Http\Requests\Auth\MagicLoginLinkRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

beforeEach(closure: function (): void
{
    $this->testUser    = testUser();

    $this->testRequest = MagicLoginLinkRequest::create(
        uri       : '/',
        parameters: [
            'id'   => $this->testUser->id,
            'hash' => sha1(string: $this->testUser->email),
        ]
    );

    $this->badRequest = MagicLoginLinkRequest::create(
        uri       : '/',
        parameters: [
            'id'   => testUser()->id,
            'hash' => sha1(string: $this->testUser->email),
        ]
    );
});

describe(description: 'MagicLoginLinkRequest', tests: function (): void
{
    it('logs in user and redirects to dashboard on valid id and hash for guest', function (): void
    {
        expect(value: Auth::check())
            ->toBeFalse();

        $response = $this->testRequest->__invoke();

        expect(value: $response)
            ->toBeInstanceOf(class: RedirectResponse::class)
            ->and(value: $response->getTargetUrl())
            ->toBe(expected: route(name: 'dashboard'))
            ->and(value: Auth::check())
            ->toBeTrue()
            ->and(value: Auth::id())
            ->toBe(expected: $this->testUser->id)
            ->and(value: Session::get(key: 'app-message'))->toBe(expected: [
                'type'    => 'success',
                'message' => 'auth.sign.in.link.success',
            ]);
    });

    it('redirects to home with error if id is invalid', function (): void
    {
        $response = $this->badRequest->__invoke();

        expect(value: $response)
            ->toBeInstanceOf(class: RedirectResponse::class)
            ->and(value: $response->getTargetUrl())
            ->toBe(expected: route(name: 'home'))
            ->and(value: Auth::check())
            ->toBeFalse()
            ->and(value: Session::get(key: 'app-message'))
            ->toBe(expected: [
                'type'    => 'error',
                'message' => 'auth.sign.in.link.invalid',
            ]);
    });

    it('redirects to home with error if hash does not match', function (): void
    {
        $response = $this->badRequest->__invoke();

        expect(value: $response)
            ->toBeInstanceOf(class: RedirectResponse::class)
            ->and(value: $response->getTargetUrl())
            ->toBe(expected: route(name: 'home'))
            ->and(value: Auth::check())
            ->toBeFalse()
            ->and(value: Session::get(key: 'app-message'))
            ->toBe(expected: [
                'type'    => 'error',
                'message' => 'auth.sign.in.link.invalid',
            ]);
    });

    it('hashMatches returns false when user is not set', function (): void
    {
        $request = MagicLoginLinkRequest::create(
            uri: '/',
            parameters: [
                'id'   => 'non-existent-id',
                'hash' => 'irrelevant',
            ]
        );

        $reflection = new ReflectionClass(objectOrClass: $request);
        $property   = $reflection->getProperty(name: 'user');

        $property->setAccessible(accessible: true);
        $property->setValue(objectOrValue: $request, value: null);

        $response = $reflection
            ->getMethod(name: 'hashMatches')
            ->invoke(object: $request);

        expect(value: $response)
            ->toBeFalse();
    });

    it('loginUserAndRedirect returns error redirect when user is not set', function (): void
    {
        $request = MagicLoginLinkRequest::create(
            uri: '/',
            parameters: [
                'id'   => 'non-existent-id',
                'hash' => 'irrelevant',
            ]
        );

        $reflection = new ReflectionClass(objectOrClass: $request);
        $property   = $reflection->getProperty(name: 'user');

        $property->setAccessible(accessible: true);
        $property->setValue(objectOrValue: $request, value: null);

        $response = $reflection
            ->getMethod(name: 'loginUserAndRedirect')
            ->invoke(object: $request);

        expect(value: $response)
            ->toBeInstanceOf(class: RedirectResponse::class)
            ->and(value: $response->getTargetUrl())
            ->toBe(expected: route(name: 'home'))
            ->and(value: Session::get(key: 'app-message'))
            ->toBe(expected: [
                'type'    => 'error',
                'message' => 'auth.sign.in.link.invalid',
            ]);
    });
});
