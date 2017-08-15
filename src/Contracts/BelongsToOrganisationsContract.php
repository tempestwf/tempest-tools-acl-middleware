<?php

namespace TempestTools\AclMiddleware\Contracts;

use Doctrine\Common\Collections\ArrayCollection;

interface BelongsToOrganisationsContract
{
    /**
     * @return ArrayCollection|OrganisationContract[]
     */
    public function getOrganisations();
}
