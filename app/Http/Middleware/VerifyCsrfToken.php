<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '*/exam/finish',
        '*/sections/*/store',
        '/upload_image_editor',
        '/qyestions_store',
        '/get_question_data',
        '*/authenticate',
        '/remove_questionorsection_data',
        '*/register-save',
        '*/authenticate',
        '/exams_set_question_value_on_session'
    ];
}
