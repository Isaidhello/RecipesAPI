<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;

class TokenMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if ($request->hasHeader('APIAuth')) {
            if (empty($request->header('APIAuth'))) {
                return returnAuthError('You must pass the Token to use the API', 401);
            } else {
                $token = $request->header('APIAuth');
                if (!$this->checkUser($token)) {
                    return returnAuthError('Invalid API Token', 403);
                }

            }
        } else if ($request->has('key')) {
            if (empty($request->get('key'))) {
                return returnAuthError('You must pass the Token to use the API', 401);
            } else {
                $token = $request->get('key');
                if (!$this->checkUser($token)) {
                    return returnAuthError('Invalid API Token', 403);
                }
            }
        } else {
            return returnAuthError('You must pass the Token to use the API', 401);
        }

        return $next($request);
    }

    private function checkUser($token) {
        $user = User::byToken($token);
        return $user->count() == 0 ? false : true;
    }
}
