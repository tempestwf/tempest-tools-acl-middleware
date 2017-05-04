<?php

namespace TempestTools\AclMiddleware\Http\Middleware;

use App\API\V3\Entities\User;
use Closure;
use Auth;
use Illuminate\Http\Request;
use LaravelDoctrine\ORM\Facades\EntityManager;

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
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        //EntityManager::
        $em = app('em');
        $em2 = \App::make(\Doctrine\ORM\EntityManager::class);
        $qb1 = $em->createQueryBuilder();
        $qb2 = $em2->createQueryBuilder();

        $controller = $request->route()->getController();
        /** @var User $user */
        $user = $controller->getUser();
        //getUser
        if ($user === NULL) {
            return response (static::ERRORS['notLoggedIn']['message'], static::ERRORS['notLoggedIn']['code']);
        }

        $actions = $request->route()->getAction();
        $uri = $request->route()->getUri();
        $requestMethod = $request->getMethod();

        $test = $user->hasPermissionTo(['shimy']);


        return $next($request);
    }
}
