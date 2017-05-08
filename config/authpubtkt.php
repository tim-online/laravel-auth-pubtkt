<?php

return array(
    /*
    |--------------------------------------------------------------------------
    | Private Key
    |--------------------------------------------------------------------------
    |
    | Contents of the private key generated for mod_auth_pubtkt.
    |
    */

    'private_key' => 'Timonline\AuthPubtkt\Ticket::privateKeyFromPath',
    'private_key_path' => '/etc/apache2/sso.pem',

    /*
    |--------------------------------------------------------------------------
    | Public Key
    |--------------------------------------------------------------------------
    |
    | Contents of the public key.
    |
    */

    'private_key' => 'Timonline\AuthPubtkt\Ticket::privateKeyFromPath',
    'private_key_path' => '/etc/apache2/sso.pem',

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Name
    |--------------------------------------------------------------------------
    |
    | Here you may change the name of the cookie used to identify a session
    | instance by ID. The name specified here will get used every time a
    | new session cookie is created by the framework for every driver.
    |
    */

    'cookie' => 'auth_pubtkt',

    /*
    |--------------------------------------------------------------------------
    | Session Lifetime
    |--------------------------------------------------------------------------
    |
    | Here you may specify the number of minutes that you wish the session
    | to be allowed to remain idle before it expires. If you want them
    | to immediately expire on the browser closing, set that option.
    |
    */

    'lifetime' => config('session.lifetime'),

    /*
    |--------------------------------------------------------------------------
    | Tokens
    |--------------------------------------------------------------------------
    |
    | Tokens stored in the ticket. The presence of a given token can be made
    | mandatory in the per-directory configuration (using the TKTAuthToken
    | directive), effectively giving a simple form of authorization.
    |
    */

    'tokens' => [],

    /*
    |--------------------------------------------------------------------------
    | Udata
    |--------------------------------------------------------------------------
    |
    | User data, for use by scripts; made available to the environment in
    | REMOTE_USER_DATA.
    |
    */

    'udata' => '',

    /*
    |--------------------------------------------------------------------------
    | Bauth
    |--------------------------------------------------------------------------
    |
    | Data from this field will be added to the request as a Basic Authorization
    | header. This can be used in reverse proxy situations where one needs
    | complete control over the username and password (see also
    | TKTAuthFakeBasicAuth, which should not be used at the same time).
    |
    */

    'bauth' => '',

    /*
    |--------------------------------------------------------------------------
    | Grace Period
    |--------------------------------------------------------------------------
    |
    | Time (in minutes) before the end of the session after which GET requests
    | will be redirected to the refresh URL (or the login URL, if no refresh URL
    | is set).
    |
    */

    'graceperiod' => 60,

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Path
    |--------------------------------------------------------------------------
    |
    | The session cookie path determines the path for which the cookie will
    | be regarded as available. Typically, this will be the root path of
    | your application but you are free to change this when necessary.
    |
    */

    'path' => config('session.path'),

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Domain
    |--------------------------------------------------------------------------
    |
    | Here you may change the domain of the cookie used to identify a session
    | in your application. This will determine which domains the cookie is
    | available to in your application. A sensible default has been set.
    |
    */

    'domain' => config('session.domain'),

    /*
    |--------------------------------------------------------------------------
    | HTTPS Only Cookies
    |--------------------------------------------------------------------------
    |
    | By setting this option to true, session cookies will only be sent back
    | to the server if the browser has a HTTPS connection. This will keep
    | the cookie from being sent to you if it can not be done securely.
    |
    */

    'secure' => config('session.secure'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Access Only
    |--------------------------------------------------------------------------
    |
    | Setting this value to true will prevent JavaScript from accessing the
    | value of the cookie and the cookie will only be accessible through
    | the HTTP protocol. You are free to modify this option if needed.
    |
    */

    'http_only' => config('session.http_only'),

);
