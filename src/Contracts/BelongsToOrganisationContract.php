<?php

namespace TempestTools\AclMiddleware\Contracts;

/**
 * @link    https://github.com/tempestwf
 * @author  William Tempest Wright Ferrer <https://github.com/tempestwf>
 */
interface BelongsToOrganisationContract
{
    /**
     * @return OrganisationContract
     */
    public function getOrganisation():OrganisationContract;
}
