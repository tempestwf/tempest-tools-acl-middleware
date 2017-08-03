<?php

namespace TempestTools\AclMiddleware\Contracts;

interface Role extends HasPermissions
{
    /**
     * @return string
     */
    public function getName():string;
}
