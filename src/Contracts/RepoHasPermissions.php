<?php

namespace TempestTools\AclMiddleware\Contracts;

interface RepoHasPermissions
{
    /**
     * @param HasId $entity
     * @param array $names
     * @param bool $requireAll
     * @return bool
     * @internal param string $permission
     */
    public function hasPermissionTo(HasId $entity, array $names, bool $requireAll = false) : bool;

}
