<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;

class SetAppLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $local = $request->header('Accept-Language', 'en');

        if (!in_array($local, ['en', 'ar'])) {
            $local = 'en';
        }
        App::setLocale($local);
        return $next($request);
    }
}
