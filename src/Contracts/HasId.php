<?php

namespace TempestTools\AclMiddleware\Contracts;

interface HasId
{
    /**
     * @return bool|int
     * @internal param Entity $entity
     * @internal param $names
     * @internal param bool $requireAll
     * @internal param string $permission
     */
    public function getId() : int ;

}
