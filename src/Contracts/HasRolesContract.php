<?php

namespace TempestTools\AclMiddleware\Contracts;

use Doctrine\Common\Collections\ArrayCollection;

interface HasRolesContract
{
    /**
     * @return ArrayCollection|RoleContract[]
     */
    public function getRoles();
}
