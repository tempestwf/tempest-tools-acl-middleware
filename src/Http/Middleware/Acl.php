<?php

namespace TempestTools\AclMiddleware\Http\Middleware;

use App\API\V3\Entities\User;
use App\API\V3\Repositories\UserRepository;
use Closure;
use Illuminate\Http\Request;

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
     * @param  Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws \RuntimeException
     */
    public function handle(Request $request, Closure $next)
    {
        $em = \App::make(\Doctrine\ORM\EntityManager::class);

        $controller = $request->route()->getController();
        /** @var User $user */
        $user = $controller->getUser();

        if ($user === NULL) {
            return response (static::ERRORS['notLoggedIn']['message'], static::ERRORS['notLoggedIn']['code']);
        }

        $actions = $request->route()->getAction();
        $uri = $request->route()->getUri();
        $requestMethod = $request->getMethod();

        //$test = $user->hasPermissionTo(['shimy']);
        /** @var UserRepository $repo */
        $repo = $em->getRepository(get_class($user));
        $repo->hasPermissionTo($user, ['shimy']);
        return $next($request);
    }
}
