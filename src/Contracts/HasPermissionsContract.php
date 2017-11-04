<?php

namespace TempestTools\Moat\Contracts;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @link    https://github.com/tempestwf
 * @author  William Tempest Wright Ferrer <https://github.com/tempestwf>
 */
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
