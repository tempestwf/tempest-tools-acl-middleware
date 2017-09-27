<?php

namespace TempestTools\AclMiddleware\Http\Middleware;

use App\API\V1\Entities\User;
use App\API\V1\Repositories\UserRepository;
use Closure;
use Illuminate\Http\Request;
use TempestTools\Common\Contracts\HasArrayHelperContract;
use TempestTools\Common\Doctrine\Utility\MakeEmTrait;
use TempestTools\Common\Helper\ArrayHelper;
use TempestTools\Common\Laravel\Utility\Extractor;

class Acl
{
    use MakeEmTrait;
    /**
     * @var array ERRORS
     * A constant that stores the errors that can be returned by the class
     */
    const ERRORS = [
        'notLoggedIn'=> [
            'message'=>'Error: User not logged in.',
            'code'=> 401,
        ],
        'permissionsFailed'=>[
            'message'=>'Error: You do not have permission to access this route.',
            'code'=> 403,
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
        $arrayHelper = new ArrayHelper();
        $laravelExtractor = new Extractor($request);
        $arrayHelper->extract([$laravelExtractor, $user]);

        $actions = $request->route()->getAction();
        $permissions = $actions['permissions'];
        $permissionsProcessed = [];
        /** @var array $permissions */
        foreach ($permissions as $permission) {
            $permissionsProcessed[] = $arrayHelper->parse($permission);
        }
        /** @var UserRepository $repo */
        $repo = $em->getRepository(get_class($user));
        $result = $repo->hasPermissionTo($user, $permissionsProcessed);

        if ($controller instanceof HasArrayHelperContract) {
            $controller->setArrayHelper($arrayHelper);
        }
        if ($result === false) {
            return response (static::ERRORS['permissionsFailed']['message'], static::ERRORS['permissionsFailed']['code']);
        }

        return $next($request);
    }
}
