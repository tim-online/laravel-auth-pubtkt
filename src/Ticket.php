<?php

namespace Timonline\AuthPubtkt;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Encryption\Encrypter;
use \Illuminate\Foundation\Auth\User;
use \Illuminate\Validation\ValidationException;

class Ticket
{

    public $uid;
    public $clientIp;
    public $validUntil;
    public $gracePeriod;
    public $tokens = [];
    public $udata = '';
    public $bauth = '';
    public $signature = '';

    // Parse a ticket into its key/value pairs and return them as an
    //     associative array for easier use.

    static function parse($string)
    {
        $data = self::explode($string);
        $ticket = new Ticket;

        if (isset($data['uid'])) {
            $ticket->uid = $data['uid'];
        }

        if (isset($data['cip'])) {
            $ticket->clientIp = $data['cip'];
        }

        if (isset($data['validuntil'])) {
            $ticket->validUntil = Carbon::createFromTimestamp($data['validuntil']);
        }

        if (isset($data['graceperiod'])) {
            $ticket->gracePeriod = Carbon::createFromTimestamp($data['graceperiod']);
        }

        if (isset($data['tokens'])) {
            $ticket->tokens = explode(',', $data['tokens']);
        }

        if (isset($data['udata'])) {
            $ticket->udata = $data['udata'];
        }

        if (isset($data['bauth'])) {
            $ticket->bauth = $data['bauth'];
        }

        if (isset($data['sig'])) {
            $ticket->signature = $data['sig'];
        }

        return $ticket;
    }

    static function implode(Array $data)
    {
        return implode(';', array_map(
            function ($v, $k) { return sprintf("%s=%s", $k, $v); },
            $data,
            array_keys($data)
        ));
    }

    static function explode($string)
    {
        $data = [];
        $kvpairs = explode(';', trim($string, ';'));

        foreach ($kvpairs as $kvpair) {
            list($key, $val) = explode('=', $kvpair, 2);
            $data[$key] = $val;
        }

        return $data;
    }

    public function toString()
    {
        if ($this->isSigned() == false) {
            $this->sign();
        }

        $this->validate();

        return "{$this->getPayload()};sig={$this->signature}";
    }

    public function validate()
    {
        $data = $this->getData();
        $validator = Validator::make($data, [
            'uid'         => 'string|required|between:1,32',
            'validuntil'  => 'date|required|after:now|after:graceperiod',
            'cip'         => 'ip|max:39',
            'tokens'      => 'string|max:255',
            'udata'       => 'string|max:255',
            'graceperiod' => 'date|after:now',
            'bauth'       => 'string',
            'sig'         =>  'string|required',
        ]);

        $validator->validate();
    }

    public function getPayload()
    {
        $data = $this->getDataWithoutSignature();
        $data['validuntil'] = $data['validuntil']->timestamp;
        $data['graceperiod'] = $data['graceperiod']->timestamp;
        $ticket = self::implode($data);
        return $ticket;
    }

    public function isSigned()
    {
        return (bool)$this->signature;
    }

    public function getDataWithoutSignature()
    {
        $data = [
            'uid'         => $this->uid,
            'cip'         => $this->clientIp,
            'validuntil'  => $this->validUntil,
            'graceperiod' => $this->gracePeriod,
            'tokens'      => implode(',', $this->tokens),
            'udata'       => $this->udata,
            'bauth'       => $this->bauth,
        ];
        return array_filter($data);
    }

    public function getData()
    {
        $data = $this->getDataWithoutSignature();
        $data['sig'] = $this->signature;
        return array_filter($data);
    }

    public function sign()
    {
        $this->signature = $this->generateSignature();
    }

    public function generateSignature()
    {
        $key = $this->getPrivateKey();
        $pkeyid = openssl_get_privatekey($key);
        if ($pkeyid === false) {
            return '';
        }

        $payload = $this->getPayload();
        $signature = '';
        $res = openssl_sign($payload, $signature, $pkeyid);
        openssl_free_key($pkeyid);
        if ($res === false) {
            return '';
        }

        return base64_encode($signature);
    }

    public function getPrivateKey()
    {
        $key = config('authpubtkt.private_key');
        if (is_callable($key)) {
            return $key();
        }
        return $key;
    }

    public static function privateKeyFromPath()
    {
        return file_get_contents(config('authpubtkt.private_key_path'));
    }

    public function getPublicKey()
    {
        $key = config('authpubtkt.public_key');
        if (is_callable($key)) {
            return $key();
        }
        return $key;
    }

    public static function publicKeyFromPath()
    {
        return file_get_contents(config('authpubtkt.public_key_path'));
    }

    public function validateSignature()
    {
        $payload = $this->getPayload();
        $signature = base64_decode($this->signature);
        $key = $this->getPublicKey();
        $keyId = openssl_get_publickey($key);
        if ($keyId === false) {
            return false;
        }

        // Returns 1 if the signature is correct, 0 if it is incorrect, and -1
        $ret = openssl_verify($payload, $signature, $keyId);
        if ($ret === -1) {
            throw new Exception(openssl_error_string());
        }

        return (bool)$ret;
    }

    // Encrypt a "bauth" passthru basic authentication value
    // (username:password) with the given key (must be exactly 16
    // characters and match the key configured on the server). The
    // result is in binary, but can be passed to pubtkt_generate()
    // directly, as it will be Base64-encoded.
    // Requires Mcrypt!
    public function encryptBauth($bauth)
    {
        $key = $this->getPrivateKey();
        if (strlen($key) != 16) {
            return null;
        }

        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_URANDOM);
        mcrypt_generic_init($td, $key, $iv);
        $encrypted = mcrypt_generic($td, $bauth);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return $iv . $encrypted;
    }

    // Decrypt a "bauth" passthru basic authentication value
    // and return only the password with the given key (must be exactly 16
    // characters). The input $bauth string should be binary,
    // so it has to be decoded using base64_decode beforehand.
    // Requires mcrypt!
    public function decryptBauth($bauth)
    {
        $key = $this->getPrivateKey();
        if (strlen($key) != 16) {
            return null;
        }

        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        $iv_s = mcrypt_enc_get_iv_size($td);
        $iv = substr($bauth, 0, $iv_s);
        $c_t = substr($bauth, $iv_s);

        mcrypt_generic_init($td, $key, $iv);
        $decrypted = mdecrypt_generic($td, $c_t);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        list($user, $pass) = explode(':', trim($decrypted));
        return $pass;
    }

}
