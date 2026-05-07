<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switch($lang)
    {
        if (array_key_exists($lang, ['en' => 'English', 'ar' => 'Arabic'])) {
            Session::put('app_locale', $lang);
        }
        return redirect()->back();
    }
}
