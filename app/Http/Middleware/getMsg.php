<?php

namespace App\Http\Middleware;

use App\msg;
use Closure;
use Illuminate\Support\Facades\Cache;

class getMsg
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
        $num = msg::where('read', 0)->count();
        Cache::put('msg_num', $num, 3600);
        return $next($request);
    }
}
