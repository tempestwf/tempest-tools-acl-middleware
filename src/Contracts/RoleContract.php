<?php

namespace TempestTools\AclMiddleware\Contracts;

interface RoleContract extends HasPermissionsContract
{
    /**
     * @return string
     */
    public function getName():string;
}
