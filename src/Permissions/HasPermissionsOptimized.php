<?php

namespace TempestTools\AclMiddleware\Permissions;

use Doctrine\ORM\EntityManager;
use LaravelDoctrine\ACL\Contracts\HasPermissions as HasPermissionsContract;
use LaravelDoctrine\ACL\Contracts\HasRoles as HasRolesHasRoles;
use LaravelDoctrine\ACL\Contracts\Permission as PermissionContract;
use RuntimeException;

trait HasPermissionsOptimized
{

    protected $needsGetIdError = 'Error: HasPermissionsOptimized must be applied to a entity that implements a getId method';

    /**
     * @param  PermissionContract|string|array $names
     * @param  bool $requireAll
     * @return bool
     * @throws \RuntimeException
     */
    public function hasPermissionTo(array $names, boolean $requireAll = false) : boolean
    {
        if (!method_exists ($this, 'getId')) {
            throw new RuntimeException($this->getNeedsGetIdError());
        }

        /** @var $em EntityManager */
        $em = \App::make(EntityManager::class);
        $qb = $em->createQueryBuilder();
        $qb->select(['e'])
            ->from(static::class, 'e')
            ->where('e.id', $this->getId());

        $wheres = [];
        if ($this instanceof HasPermissionsContract) {
            $qb->leftJoin('e.Permissions', 'p');
            $wheres[] = $qb->expr()->in('p', $names);
        }

        if ($this instanceof HasRolesHasRoles) {
            $qb->leftJoin('e.Roles', 'r');
            $qb->leftJoin('r.Permissions', 'p2');
            $wheres[] = $qb->expr()->in('p2', $names);
        }

        $qb->andWhere(
            $qb->expr()->orX($wheres)
        );
        /** @var array[] $results */
        $results = $qb->getQuery()->getArrayResult();

        if (count($results) === 0) {
            return false;
        }

        if ($requireAll === true) {
            $matchedPermissionsCount = 0;
            if (array_key_exists('Permissions', $results)) {
                $matchedPermissionsCount += count($results['Permissions']);
            }
            if (array_key_exists('Roles', $results)) {
                foreach ($results['Roles'] as $key => $value) {
                    if (array_key_exists('Permissions', $value)) {
                        $matchedPermissionsCount += count($value['Permissions']);
                    }
                }
            }
            return $matchedPermissionsCount === count($names);
        }

        return true;
    }

    /**
     * @return string
     */
    public function getNeedsGetIdError(): string
    {
        return $this->needsGetIdError;
    }

    /**
     * @param PermissionContract|string $permission
     *
     * @return string
     */
    protected function getPermissionName($permission)
    {
        return $permission instanceof PermissionContract ? $permission->getName() : $permission;
    }
}
