<?php

namespace TempestTools\AclMiddleware\Contracts;

use Doctrine\Common\Collections\ArrayCollection;

interface HasRoles
{
    /**
     * @return ArrayCollection|Role[]
     */
    public function getRoles();
}
