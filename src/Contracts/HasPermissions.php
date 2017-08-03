<?php

namespace TempestTools\AclMiddleware\Contracts;

use Doctrine\Common\Collections\ArrayCollection;

interface HasPermissions
{
    /**
     * @param string $permission
     *
     * @return bool
     */
    public function hasPermissionTo($permission):bool;

    /**
     * @return ArrayCollection|Permission[]
     */
    public function getPermissions();
}
