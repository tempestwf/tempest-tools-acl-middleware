<?php

namespace TempestTools\AclMiddleware\Contracts;

interface Permission
{
    /**
     * @return string
     */
    public function getName():string;
}
