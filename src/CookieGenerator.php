<?php

namespace Timonline\AuthPubtkt;

use Carbon\Carbon;
use \Illuminate\Foundation\Auth\User;
use \Symfony\Component\HttpFoundation\Cookie;

class CookieGenerator
{

    public function forUser(User $user)
    {
        return new Cookie(
            $this->getCookieName(),
            $this->getCookieValue($user),
            $this->getCookieExpirationDate(),
            $this->getCookiePath(),
            $this->getCookieDomain(),
            $this->getCookieSecure(),
            $this->getCookieHttpOnly()
        );
    }

    public function getCookieName()
    {
        return config('authpubtkt.cookie');
    }

    public function getCookieValue(User $user)
    {
        $ticket = TicketGenerator::forUser($user);
        return $ticket->toString();
    }

    public function getCookieExpirationDate()
    {
        return Carbon::now()->addMinutes(config('authpubtkt.lifetime'));
    }

    public function getCookiePath()
    {
        return config('authpubtkt.path');
    }

    public function getCookieDomain()
    {
        return config('authpubtkt.domain');
    }

    public function getCookieSecure()
    {
        return config('authpubtkt.secure');
    }

    public function getCookieHttpOnly()
    {
        return config('authpubtkt.http_only');
    }

}
