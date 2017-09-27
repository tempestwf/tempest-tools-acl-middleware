<?php

namespace TempestTools\AclMiddleware\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use TempestTools\AclMiddleware\Contracts\HasIdContract;
use TempestTools\AclMiddleware\Contracts\RepoHasPermissionsContract;
use TempestTools\Common\Contracts\ArrayHelperContract;
use TempestTools\Common\Contracts\HasArrayHelperContract;
use TempestTools\Common\Contracts\HasUserContract;
use TempestTools\Common\Doctrine\Utility\MakeEmTrait;
use TempestTools\Common\Exceptions\Laravel\Http\Middleware\CommonMiddlewareException;
use TempestTools\Common\Helper\ArrayHelper;

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
        $controller = $request->route()->getController();

        if ($controller instanceof HasArrayHelperContract === false) {
            throw CommonMiddlewareException::controllerDoesNotImplementHasArrayHelperContract();
        }

        if ($controller instanceof HasUserContract === false) {
            throw CommonMiddlewareException::controllerDoesNotImplementHasUserContract();
        }

        /** @var HasIdContract $user */
        $user = $controller->getUser();

        if ($user === NULL) {
            return response (static::ERRORS['notLoggedIn']['message'], static::ERRORS['notLoggedIn']['code']);
        }
        $arrayHelper = $controller->getArrayHelper() ?? new ArrayHelper();

        if ($controller instanceof HasArrayHelperContract) {
            $controller->setArrayHelper($arrayHelper);
        }

        $extra = ['self'=>$this, 'controller'=>$controller, 'arrayHelper'=>$arrayHelper];

        $result = $this->checkDBPermissions($request, $arrayHelper, $user, $extra);
        $result = $result === true?$result:$this->checkPermissionClosures($request, $arrayHelper, $extra);
        if ($result === false) {
            return response (static::ERRORS['permissionsFailed']['message'], static::ERRORS['permissionsFailed']['code']);
        }

        return $next($request);
    }

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param Request $request
     * @param ArrayHelperContract $arrayHelper
     * @param HasIdContract $user
     * @param array $extra
     * @return bool
     * @throws \RuntimeException
     */
    protected function checkDBPermissions(Request $request, ArrayHelperContract $arrayHelper, HasIdContract $user, array $extra):bool
    {
        $em = $this->em();
        $actions = $request->route()->getAction();
        $permissions = $actions['permissions'];
        $permissionsProcessed = [];
        /** @var array $permissions */
        foreach ($permissions as $permission) {
            $permissionsProcessed[] = $arrayHelper->parse($permission, $extra);
        }
        /** @var RepoHasPermissionsContract $repo */
        $repo = $em->getRepository(get_class($user));
        return $repo->hasPermissionTo($user, $permissionsProcessed);
    }

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param Request $request
     * @param ArrayHelperContract $arrayHelper
     * @param array $extra
     * @return bool
     * @internal param HasArrayHelperContract $controller
     */
    protected function checkPermissionClosures (Request $request, ArrayHelperContract $arrayHelper, array $extra):bool
    {
        $actions = $request->route()->getAction();
        if (isset($actions['permissionClosures']) === true && is_array($actions['permissionClosures']) === true ) {
            /** @var array $permissionClosures */
            $permissionClosures = $actions['permissionClosures'];
            foreach($permissionClosures as $closure) {
                $result = $arrayHelper->parseClosure($closure, $extra);
                if ($result === false) {
                    return false;
                }
            }
        }
        return true;
    }
}
