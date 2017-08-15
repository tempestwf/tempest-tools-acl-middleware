<?php

namespace TempestTools\AclMiddleware\Contracts;

use Doctrine\Common\Collections\ArrayCollection;

interface HasPermissionsContract
{
    /**
     * @param string $permission
     *
     * @return bool
     */
    public function hasPermissionTo($permission):bool;

    /**
     * @return ArrayCollection|PermissionContract[]
     */
    public function getPermissions();
}
