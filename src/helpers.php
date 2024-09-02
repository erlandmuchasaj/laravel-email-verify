<?php

use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Indisposable;

if (! function_exists('email_verify')) {
    function email_verify(string $email): bool
    {
        $verifier = new Indisposable();

        return $verifier->isRealEmail($email);
    }
}
