<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $locale = $request->cookie('locale');
        if ($locale && in_array($locale, ['en', 'it', 'ru'])) {
            App::setLocale($locale);
        }
        return $next($request);
    }
}
