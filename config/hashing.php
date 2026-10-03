<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Hash Driver
    |--------------------------------------------------------------------------
    */

    'driver' => 'bcrypt',

    /*
    |--------------------------------------------------------------------------
    | Bcrypt Options
    |--------------------------------------------------------------------------
    |
    | "verify" => false : certains comptes de démo ont été hashés avec le
    | préfixe $2b$ (ex: via un script Python) plutôt que $2y$ (PHP natif).
    | Ce sont des hachages bcrypt tout à fait valides, mais la fonction PHP
    | password_get_info() ne reconnaît officiellement que $2y$ comme
    | "bcrypt", ce qui fait échouer la vérification stricte de Laravel par
    | erreur. On désactive cette vérification de préfixe : password_verify()
    | continue de comparer le mot de passe normalement et en toute sécurité.
    |
    */

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12),
        'verify' => false,
    ],

    'argon' => [
        'memory' => 65536,
        'threads' => 1,
        'time' => 4,
        'verify' => true,
    ],

];