<?php

namespace TempestTools\AclMiddleware\Repository;

use TempestTools\AclMiddleware\Contracts\HasIdContract;
use TempestTools\AclMiddleware\Helper\HasPermissionsQueryHelper;


/**
 * Trait that allows you to check for permissions on an entity through the repo instead of just on the Entity
 *
 * @link    https://github.com/tempestwf
 * @author  William Tempest Wright Ferrer <https://github.com/tempestwf>
 */
trait HasPermissionsQueryTrait
{
    /**
     * A method that checks if the current entity the trait is applied to has permissions that match the names passed
     *
     * @param HasIdContract $entity
     * @param  array $names
     * @param  bool $requireAll
     * @return bool
     * @throws \RuntimeException
     */
    public function hasPermissionTo(HasIdContract $entity, array $names, bool $requireAll = false) : bool
    {
       $hasPermissionsQueryHelp = new HasPermissionsQueryHelper($this);
       return $hasPermissionsQueryHelp->hasPermissionTo($entity, $names, $requireAll);
    }


}
