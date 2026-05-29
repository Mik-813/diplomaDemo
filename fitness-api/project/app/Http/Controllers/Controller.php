<?php

namespace App\Http\Controllers;

use App\Rules\Recaptcha;

abstract class Controller
{
    protected function getRecaptchaRules(): array
    {
        $permit = env('LOCAL_RECAPTCHA_PERMIT');

        if ($permit === true) {
            return ['nullable'];
        }

        if ($permit === false) {
            return ['required', function ($attribute, $value, $fail) {
                $fail('The recaptcha token is invalid.');
            }];
        }

        return ['required', new Recaptcha];
    }
}
