<?php

namespace Timonline\AuthPubtkt;

use Carbon\Carbon;
use \Illuminate\Foundation\Auth\User;
use Request;

class TicketGenerator
{

    static public function forUser(User $user)
    {
        $generator = new TicketGenerator;
        $ticket = new Ticket;
        $ticket->uid = $generator->getUid($user);
        $ticket->clientIp = $generator->getClientIp($user);
        $ticket->validUntil = $generator->getValidUntil($user);
        $ticket->gracePeriod = $generator->getGracePeriod($user);
        $ticket->tokens = $generator->getTokens($user);
        $ticket->udata = $generator->getUdata($user);
        $ticket->bauth = $generator->getBauth($user);
        return $ticket;
    }

    public function getUid(User $user)
    {
        return (string)$user->id;
    }

    public function getClientIp(User $user)
    {
        return Request::ip();
    }

    public function getValidUntil(User $user)
    {
        $minutes = config('authpubtkt.lifetime');
        return Carbon::now()->addMinutes($minutes);
    }

    public function getGracePeriod(User $user)
    {
        $minutes = config('authpubtkt.graceperiod');
        return $this->getValidUntil($user)->subMinutes($minutes);
    }

    public function getTokens(User $user)
    {
        return config('authpubtkt.tokens');
    }

    public function getUdata(User $user)
    {
        $udata = config('authpubtkt.udata');
        if (is_callable($udata)) {
            return $udata($user);
        }

        return $udata;
    }

    public function getBauth(User $user)
    {
        return config('authpubtkt.bauth');
    }

    public static function userData(User $user)
    {
        return json_encode([
            'name' => $user->name,
            'email' => $user->email,
            'current_team_id' => $user->current_team_id,
        ]);
    }
}
