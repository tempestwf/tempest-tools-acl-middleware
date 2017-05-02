<?php

namespace TempestTools\AclMiddleware\Http\Middleware;

use Closure;

class Acl
{
    /**
     * @var array ERRORS
     * A constant that stores the errors that can be returned by the class
     */
    const ERRORS = [
        'notLoggedIn'=>
            [
                'message'=>'Error: User not logged in',
                'code'=> 401
            ]
    ];
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        if ($user === NULL) {
            return response (static::ERRORS['notLoggedIn']['message'], static::ERRORS['notLoggedIn']['code']);
        }

        $actions = $request->route()->getAction();
        $uri = $request->route()->getUri();
        $requestMethod = $request->getMethod();
        $actionName = $request->route()->getActionName();

        $currentAction = \Route::currentRouteAction();
        list($controller, $method) = explode('@', $currentAction);
        $controller = preg_replace('/.*\\\/', '', $controller);
        $user->can();
        return $next($request);
    }
}
