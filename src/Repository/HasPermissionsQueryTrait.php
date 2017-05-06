<?php

namespace TempestTools\AclMiddleware\Repository;

use App\Entities\Entity;
use TempestTools\AclMiddleware\Helper\HasPermissionsQueryHelper;


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
