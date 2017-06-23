<?php

namespace TempestTools\AclMiddleware\Contracts;

use Doctrine\Common\Collections\ArrayCollection;

interface BelongsToOrganisations
{
    /**
     * @return ArrayCollection|Organisation[]
     */
    public function getOrganisations();
}
