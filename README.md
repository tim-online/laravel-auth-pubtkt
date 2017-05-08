# Laravel mod_auth_pubtkt module

This module implements the login server for the Apache
[mod_auth_pubtkt](https://neon1.net/mod_auth_pubtkt/) module.

It works by setting an additional auth_pubtkt cookie when logging in.

## Installation

This package can be installed through Composer.

``` bash
composer require tim-online/laravel-auth-pubtkt
```

You must install this service provider.

``` php
// config/app.php
'providers' => [
    ...
    Timonline\AuthPubtkt\AuthPubtktServiceProvider::class
    ...
];
```

You can publish the config file of this package with this command:

``` bash
php artisan vendor:publish --provider="Timonline\AuthPubtkt\AuthPubtktServiceProvider"
```

This module works with the default Laravel login form but it needs some
customisations to make the redirect to the protected application work properly.

Allow the auth_pubtkt cookie to be unencrypted. Add the cookienaam as an
exception to `EncryptCookies`:

``` php
/**
 * The names of the cookies that should not be encrypted.
 *
 * @var array
 */
protected $except = [
    'auth_pubtkt',
];
```

Add the `back` parameter as a hidden input to your login form:

``` blade
<input type="hidden" name="back" value="{{ app('request')->input('back') }}" />
```

And finally, after login, redirect to the back url. Edit your
`Auth\LoginController`:

``` php
protected function redirectTo(Request $request)
{
    return $request->input('back', '/home');
}
```

To make the redirect work in Spark you can edit `SparkServiceProvider` and add
this call in the `booted` method:

``` php
Spark::afterLoginRedirectTo(function() {
    $request = app('request');
    return $request->input('back', '/home');
});
```

To secure the protected application you can use something like this:

``` apache
<Location />
    AuthType mod_auth_pubtkt
    TKTAuthLoginURL https://myapp.tld/login
    TKTAuthTimeoutURL https://myapp.tld/login?timeout=1
    TKTAuthRefreshURL https://myapp.tld/login?refresh=1
    TKTAuthUnauthURL https://myapp.tld/login?unauth=1
    TKTAuthRequireSSL on
    require valid-user
</Location>
```

## TODO

- make `?back=` work without manual customisations in view and controller
- make the EncryptCookies middleware automatically skip the auth_pubtkt cookie
- create a custom Laravel authentication guard for mod_auth_pubtkt
- Add timeout, refresh & unauth notifications
