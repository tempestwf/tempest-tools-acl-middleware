<?php

namespace TempestTools\AclMiddleware\Contracts;

use App\Entities\Entity;

interface RepoHasPermissions
{
    /**
     * @param Entity $entity
     * @param $names
     * @param bool $requireAll
     * @return bool
     * @internal param string $permission
     *
     */
    public function hasPermissionTo(Entity $entity, $names, $requireAll = false) : bool;

}
