<?php

namespace TempestTools\AclMiddleware\Entity;

use App\Entities\Entity;
use Doctrine\ORM\EntityManager;
use TempestTools\AclMiddleware\Contracts\RepoHasPermissionsContract;
use TempestTools\AclMiddleware\Exceptions\AclMiddlewareException;
use TempestTools\Common\Doctrine\Utility\MakeEmTrait;


trait HasPermissionsOptimizedTrait
{
    use MakeEmTrait;

    /**
     * A method that checks if the current entity the trait is applied to has permissions that match the names passed
     *
     * @param  array $names
     * @param  bool $requireAll
     * @return bool
     * @throws \TempestTools\AclMiddleware\Exceptions\AclMiddlewareException
     * @internal param Entity $entity
     */
    public function hasPermissionTo($names, $requireAll = false) : bool
    {
        if (!$this instanceof Entity) {
            throw AclMiddlewareException::hasPermissionsOptimizedTraitMustBeAppliedToEntity();
        }
        /** @var EntityManager $em */
        $em = $this->em();
        /** @var RepoHasPermissionsContract $repo */
        $repo = $em->getRepository(get_class($this));
        return $this->hasPermissionToFromRepo($repo, $names, $requireAll);
    }

    /**
     * @param RepoHasPermissionsContract $repo
     * @param $names
     * @param bool $requireAll
     * @return bool
     */
    protected function hasPermissionToFromRepo(RepoHasPermissionsContract $repo, array $names, bool $requireAll = false) : bool
    {
        /** @noinspection PhpParamsInspection */
        return $repo->hasPermissionTo($this, $names, $requireAll);
    }


}
