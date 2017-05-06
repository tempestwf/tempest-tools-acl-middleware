<?php

namespace TempestTools\AclMiddleware\Permissions;

use App\Entities\Entity;


trait HasPermissionsQueryTrait
{
    /**
     * A method that checks if the current entity the trait is applied to has permissions that match the names passed
     *
     * @param Entity $entity
     * @param  array $names
     * @param  bool $requireAll
     * @return bool
     * @throws \RuntimeException
     */
    public function hasPermissionTo(Entity $entity, $names, $requireAll = false) : bool
    {
       $hasPermissionsQueryHelp = new HasPermissionsQueryHelper($this);
       return $hasPermissionsQueryHelp->hasPermissionTo($entity, $names, $requireAll);
    }


}
