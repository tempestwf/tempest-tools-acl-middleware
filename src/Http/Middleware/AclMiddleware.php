<?php

namespace TempestTools\Moat\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use TempestTools\Moat\Contracts\HasIdContract;
use TempestTools\Moat\Contracts\RepoHasPermissionsContract;
use TempestTools\Common\ArrayObject\DefaultTTArrayObject;
use TempestTools\Common\Contracts\ArrayHelperContract;
use TempestTools\Common\Contracts\HasArrayHelperContract;
use TempestTools\Common\Contracts\HasUserContract;
use TempestTools\Common\Doctrine\Utility\MakeEmTrait;
use TempestTools\Common\Exceptions\Laravel\Http\Middleware\CommonMiddlewareException;
use TempestTools\Common\Helper\ArrayHelper;

/**
 * Middleware that can be applied to a route to check if the current user has permission to access that route.
 *
 * @link    https://github.com/tempestwf
 * @author  William Tempest Wright Ferrer <https://github.com/tempestwf>
 */
class AclMiddleware
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
            throw CommonMiddlewareException::controllerDoesNotImplement('HasArrayHelperContract');
        }

        if ($controller instanceof HasUserContract === false) {
            throw CommonMiddlewareException::controllerDoesNotImplement('HasUserContract');
        }

        /** @var HasIdContract $user */
        $user = $controller->getUser();

        if ($user === NULL) {
            return response (static::ERRORS['notLoggedIn']['message'], static::ERRORS['notLoggedIn']['code']);
        }
        $arrayHelper = $controller->getArrayHelper() ?? new ArrayHelper(new DefaultTTArrayObject());

        $controller->setArrayHelper($arrayHelper);

        $extra = ['self'=>$this, 'controller'=>$controller, 'arrayHelper'=>$arrayHelper];

        $result = $this->checkPermissionClosures($request, $arrayHelper, $extra);
        $result = $result === true?$this->checkDBPermissions($request, $arrayHelper, $user, $extra):$result;

        if ($result === false) {
            return response (static::ERRORS['permissionsFailed']['message'], static::ERRORS['permissionsFailed']['code']);
        }

        return $next($request);
    }

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * Checks the permissions in the DB
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
        $permissions = $actions['permissions'] ?? [];
        if ($permissions === []) {
            return true;
        }
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
     * Checks permissions by running through closures applied to the route to make sure they all evaluate true.
     * @param Request $request
     * @param ArrayHelperContract $arrayHelper
     * @param array $extra
     * @return bool
     * @throws \RuntimeException
     * @internal param HasArrayHelperContract $controller
     */
    protected function checkPermissionClosures (Request $request, ArrayHelperContract $arrayHelper, array $extra):bool
    {
        $actions = $request->route()->getAction();
        /** @var array $permissionClosures */
        $permissionClosures = $actions['permissionClosures'] ?? [];
        foreach($permissionClosures as $closure) {
            $result = $arrayHelper->parse($closure, $extra);
            if ($result === false) {
                return false;
            }
        }
        return true;
    }
}
