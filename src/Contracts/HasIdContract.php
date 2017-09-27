<?php

namespace TempestTools\AclMiddleware\Contracts;

interface HasIdContract
{
    /**
     * @return mixed
     * @internal param Entity $entity
     * @internal param $names
     * @internal param bool $requireAll
     * @internal param string $permission
     */
    public function getId();

}