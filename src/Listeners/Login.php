<?php

namespace Timonline\AuthPubtkt\Listeners;

use \Symfony\Component\HttpFoundation\Cookie;
use \Illuminate\Foundation\Auth\User;
use Illuminate\Http\Response;
use \Illuminate\Http\Request;

class Login
{

    protected $request;
    protected $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Handle the event.
     *
     * @param  OrderShipped  $event
     * @return void
     */
    public function handle(\Illuminate\Auth\Events\Login $event)
    {
        $generator = new \Timonline\AuthPubtkt\CookieGenerator;
        $cookie = $generator->forUser($event->user);
        $this->response->headers->setCookie($cookie);
        return;
    }

}
