<?php

namespace TempestTools\AclMiddleware\Contracts;

interface RepoHasPermissionsContract
{
    /**
     * @param HasIdContract $entity
     * @param array $names
     * @param bool $requireAll
     * @return bool
     * @internal param string $permission
     */
    public function hasPermissionTo(HasIdContract $entity, array $names, bool $requireAll = false) : bool;

}
