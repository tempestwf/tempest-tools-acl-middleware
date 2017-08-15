<?php

namespace TempestTools\AclMiddleware\Contracts;

interface PermissionContract
{
    /**
     * @return string
     */
    public function getName():string;
}
