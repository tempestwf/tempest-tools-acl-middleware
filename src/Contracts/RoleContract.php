<?php

namespace TempestTools\AclMiddleware\Contracts;

/**
 * @link    https://github.com/tempestwf
 * @author  William Tempest Wright Ferrer <https://github.com/tempestwf>
 */
interface RoleContract extends HasPermissionsContract
{
    /**
     * @return string
     */
    public function getName():string;
}
