<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class JsonCheck
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
        if (!$request->isMethod('post'))
        { return $next($request);
        }


        $acceptHeader = $request->header('Accept');
        if ($acceptHeader != 'application/json') {
            return response()->json(["msg"=>"Only JSON requests are allowed"], 406);
        }

        return $next($request);
        // return $next($request);
    }
}
