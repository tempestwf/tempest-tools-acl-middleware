<?php

namespace TempestTools\AclMiddleware\Http\Middleware;

use App\API\V3\Entities\User;
use App\API\V3\Repositories\UserRepository;
use Closure;
use Illuminate\Http\Request;
use TempestTools\Common\Doctrine\Utility\MakeEmTrait;

class Acl
{
    use MakeEmTrait;
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
        $em = $this->em();

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
        $result1 = $repo->hasPermissionTo($user, ['auth/me:GET', 'auth/authenticate:POST', 'shimy'], false);
        //$result2 = $user->hasPermissionTo(['auth/me:GET']);

        return $next($request);
    }
}
