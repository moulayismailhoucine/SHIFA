<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Session::has('app_locale')) {
            App::setLocale(Session::get('app_locale'));
        } else {
            // Default to Arabic for this project as it's often the target
            App::setLocale('en');
        }

        return $next($request);
    }
}
