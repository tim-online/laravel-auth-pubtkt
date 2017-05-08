<?php

namespace Timonline\AuthPubtkt\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Session\Middleware\StartSession;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class AuthPubtktCookie extends StartSession
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $response = $next($request);
        $user = $request->user();

        if (!$user) {
            return $response;
        }

        $generator = new \Timonline\AuthPubtkt\CookieGenerator;
        if (\Cookie::get($generator->getCookieName())) {
            return $response;
        }

        $cookie = $generator->forUser($request->user());
        $response->headers->setCookie($cookie);
        return $response;
    }

}
