<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Switch the application locale to the one the client asked for, so that
     * response messages and validation errors come back in that language.
     *
     * The X-Locale header wins; otherwise the best Accept-Language match is used.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var array<int, string> $supportedLocales */
        $supportedLocales = config('app.supported_locales', ['en']);

        $locale = $request->header('X-Locale');

        if (! in_array($locale, $supportedLocales, true)) {
            $locale = $request->getPreferredLanguage($supportedLocales);
        }

        if (in_array($locale, $supportedLocales, true)) {
            App::setLocale($locale);
        }

        $response = $next($request);
        $response->headers->set('Content-Language', App::getLocale());

        return $response;
    }
}
