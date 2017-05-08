<?php

namespace Timonline\AuthPubtkt\Listeners;

use \Symfony\Component\HttpFoundation\Cookie;
use \Illuminate\Foundation\Auth\User;
use Illuminate\Http\Response;

class Logout
{

    protected $response;

    public function __construct(\Illuminate\Http\Response $response)
    {
        $this->response = $response;
    }

    /**
     * Handle the event.
     *
     * @param  OrderShipped  $event
     * @return void
     */
    public function handle(\Illuminate\Auth\Events\Logout $event)
    {
        $generator = new \Timonline\AuthPubtkt\CookieGenerator;
        $name = $generator->getCookieName();
        $path = $generator->getCookiePath();
        $domain = $generator->getCookieDomain();
        \Cookie::queue(
            \Cookie::forget($name, $path, $domain)
        );
    }


}
