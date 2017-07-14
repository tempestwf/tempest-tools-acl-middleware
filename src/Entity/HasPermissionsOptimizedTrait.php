<?php

namespace TempestTools\AclMiddleware\Entity;

use App\Entities\Entity;
use Doctrine\ORM\EntityManager;
use TempestTools\AclMiddleware\Contracts\RepoHasPermissions;
use TempestTools\Common\Doctrine\Utility\MakeEmTrait;


trait HasPermissionsOptimizedTrait
{
    use MakeEmTrait;
    protected $hasPermissionsOptimizedTriatMustBeAppliedToEntity = 'Error: HasPermissionsOptimizedTriat must be applied to an entity';
    /**
     * A method that checks if the current entity the trait is applied to has permissions that match the names passed
     *
     * @param  array $names
     * @param  bool $requireAll
     * @return bool
     * @throws \RuntimeException
     * @internal param Entity $entity
     */
    public function hasPermissionTo($names, $requireAll = false) : bool
    {
        if (!$this instanceof Entity) {
            throw new \RuntimeException($this->getHasPermissionsOptimizedTriatMustBeAppliedToEntity());
        }
        /** @var EntityManager $em */
        $em = $this->em();
        /** @var RepoHasPermissions $repo */
        $repo = $em->getRepository(get_class($this));
        return $this->hasPermissionToFromRepo($repo, $names, $requireAll);
    }

    /**
     * @param RepoHasPermissions $repo
     * @param $names
     * @param bool $requireAll
     * @return bool
     */
    protected function hasPermissionToFromRepo(RepoHasPermissions $repo, array $names, bool $requireAll = false) : bool
    {
        return $repo->hasPermissionTo($this, $names, $requireAll);
    }

    /**
     * @return string
     */
    public function getHasPermissionsOptimizedTriatMustBeAppliedToEntity(): string
    {
        return $this->hasPermissionsOptimizedTriatMustBeAppliedToEntity;
    }


}
