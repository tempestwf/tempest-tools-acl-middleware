<?php

namespace TempestTools\AclMiddleware\Contracts;

interface BelongsToOrganisation
{
    /**
     * @return Organisation
     */
    public function getOrganisation():Organisation;
}
