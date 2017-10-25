<?php

namespace TempestTools\AclMiddleware\Contracts;

/**
 * @link    https://github.com/tempestwf
 * @author  William Tempest Wright Ferrer <https://github.com/tempestwf>
 */
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
