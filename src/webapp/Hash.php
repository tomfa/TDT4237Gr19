<?php

namespace tdt4237\webapp;

class Hash
{
    function __construct()
    {
    }

    static function make($plaintext)
    {
        $options = [
            'cost' => 11,
            'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
        ];

        return password_hash($plaintext, PASSWORD_BCRYPT, $options);

    }

    static function check($plaintext, $hash)
    {
        return password_verify($plaintext, $hash);
    }
}
