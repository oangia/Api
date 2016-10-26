<?php

namespace oangia\Api\Middleware;

use App\Models\User;
use Auth;
use Closure;

class AuthenticateWithToken
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
        if (! $request->header('Authorization', null)) {
            return response()->json(
                [
                    'code'    => 403,
                    'message' => 'Invalid user token. Please login again!!!',
                    'data'    => [],
                ], 403
            );
        }

        $user = User::where('remember_token', str_replace('token=', '', $request->header('Authorization', null)))
                        ->first();

        if (! $user) {
            return response()->json(
                [
                    'code'    => 403,
                    'message' => 'Invalid user token. Please login again!!!',
                    'data'    => [],
                ], 403
            );
        }

        if ($user->ban) {
            return response()->json(
                [
                    'code'    => 403,
                    'message' => 'Your account has been banned',
                    'data'    => [],
                ], 403
            );
        }

        Auth::guard('api')->login($user);

        return $next($request);
    }
}
