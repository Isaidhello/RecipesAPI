<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;

class TokenMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     *    HTTP Request Data.
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next) {
        /** Check if the Token is on Header */
        if ($request->hasHeader('APIAuth')) {

            /** If the Header is present, but the value is empty */
            if (empty($request->header('APIAuth'))) {
                /** return error message */
                return serviceErrorMessage('You must pass the Token to use the API', 401);
            } else {
                /** Otherwise, get the token, and try to load the user via Token */
                $token = $request->header('APIAuth');

                /** If the function checkUser returns false, any users were loaded :( */
                if (!$this->checkUser($token)) {
                    /** return error message */
                    return serviceErrorMessage('Invalid API Token', 403);
                }

            }
            /** If token is not on Header, try to get via queryString */
        } else if ($request->has('key')) {

            /** If the queryString is present, but is empty... */
            if (empty($request->get('key'))) {
                /** return error message */
                return serviceErrorMessage('You must pass the Token to use the API', 401);
            } else {
                /** Otherwise, get the token, and try to load the user via Token */
                $token = $request->get('key');

                /** If the function checkUser returns false, any users were loaded :( */
                if (!$this->checkUser($token)) {
                    /** return error message */
                    return serviceErrorMessage('Invalid API Token', 403);
                }
            }
        } else {
            return serviceErrorMessage('You must pass the Token to use the API', 401);
        }

        /** If get here, everything is fine, the Token is right and user exists, Go ahead bro! :D */
        return $next($request);
    }

    /**
     * Check if current token belongs to a registered user.
     *
     * @param string $token
     *    Given user token.
     * @return bool
     *    Either TRUE if user is authenticated or FALSE.
     */
    private function checkUser($token) {
        /** try to load user via Query Scope */
        $user = User::byToken($token);

        /** If count > 0, there exists an User with a valid token */
        return $user->count() == 0 ? false : true;
    }
}
