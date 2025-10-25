<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has a locale preference
        if (auth()->check() && auth()->user()->locale) {
            $locale = auth()->user()->locale;
        }
        // Otherwise check session
        elseif (Session::has('locale')) {
            $locale = Session::get('locale');
        }
        // Otherwise use default from config
        else {
            $locale = config('app.locale', 'ar');
        }

        // Validate locale is supported
        $supportedLocales = ['ar', 'en'];
        if (!in_array($locale, $supportedLocales)) {
            $locale = config('app.locale', 'ar');
        }

        // Set application locale
        App::setLocale($locale);

        // Set Carbon locale for date formatting
        if (class_exists('\Carbon\Carbon')) {
            \Carbon\Carbon::setLocale($locale);
        }

        // Set text direction in config for use in views
        config(['app.text_direction' => $locale === 'ar' ? 'rtl' : 'ltr']);

        return $next($request);
    }
}
