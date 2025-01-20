<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class IfSubdomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Session::has("subdomain") || $request->route('subdomain') != null) {
            $url = $request->url();
            if (Session::has('subdomain') == false)
                Session::put('subdomain', $request->route('subdomain'));

                $urlParts = parse_url($url);            
                $host = $urlParts['host'];
            
                $newUrl = 'https://' . Session::get('subdomain') . '.' . env('APP_DOMAIN') . $urlParts['path'];
                if (isset($urlParts['query'])) {
                    $newUrl .= '?' . $urlParts['query'];
                }
                return redirect($newUrl);
        }else{
            return $next($request);
        }
    }
}
