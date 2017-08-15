<?php

namespace TempestTools\AclMiddleware\Contracts;

interface BelongsToOrganisationContract
{
    /**
     * @return OrganisationContract
     */
    public function getOrganisation():OrganisationContract;
}
