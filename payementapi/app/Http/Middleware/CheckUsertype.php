<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUsertype
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $type): Response
    {
        $token=$request->header('Authorization');

        $customer=\App\Models\Customer::where('token',$token)->first();

        if(!$customer){
            //token is invalid
            return response()->json(['error'=>'Unauthorized'],401);
        }
        //Check user type
        if (($type==='admin' || $type==='user') && ($customer->usertype !=='admin' || $customer->usertype !=='user')){
            return $next($request);
        }

        //allow access
        return response()->json(['error'=>'Forbidden'],403);
    }
}
