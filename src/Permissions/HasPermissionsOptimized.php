<?php

namespace TempestTools\AclMiddleware\Permissions;

use Doctrine\ORM\EntityManager;
use LaravelDoctrine\ACL\Contracts\HasPermissions as HasPermissionsContract;
use LaravelDoctrine\ACL\Contracts\HasRoles as HasRolesHasRoles;
use LaravelDoctrine\ACL\Contracts\Permission as PermissionContract;
use RuntimeException;

trait HasPermissionsOptimized
{

    /**
     * @var string
     */
    protected $needsGetIdError = 'Error: HasPermissionsOptimized must be applied to a entity that implements a getId method';

    /**
     * @param  PermissionContract|string|array $names
     * @param  bool $requireAll
     * @return bool
     * @throws \RuntimeException
     */
    public function hasPermissionTo(array $names, boolean $requireAll = false) : boolean
    {
        // If you can't get the id from the entity then this trait is not compatible with the class
        if (!method_exists ($this, 'getId')) {
            throw new RuntimeException($this->getNeedsGetIdError());
        }

        // We use a query to check if the user has the permissions that are passed rather than using the getRoles and getPermissions methods used previously.
        // This method will be much faster when there are many permissions assigned to the user/role.
        /** @var $em EntityManager */
        $em = \App::make(EntityManager::class);
        $qb = $em->createQueryBuilder();
        $qb->select(['e'])
            ->from(static::class, 'e')
            ->where('e.id', $this->getId());

        $wheres = [];
        // If we have the HasPermissionsContract then we know that permissions can be assigned by a Permissions relation
        if ($this instanceof HasPermissionsContract) {
            $qb->leftJoin('e.Permissions', 'p');
            $wheres[] = $qb->expr()->in('p', $names);
        }

        // If we have the HasRolesHasRoles then we know that permissions can be assigned by a Roles relation
        if ($this instanceof HasRolesHasRoles) {
            $qb->leftJoin('e.Roles', 'r');
            $qb->leftJoin('r.Permissions', 'p2');
            $wheres[] = $qb->expr()->in('p2', $names);
        }
        // Add the wheres for either roles or permissions or both depending which contracts were present.

        $qb->andWhere(
            $qb->expr()->orX($wheres)
        );
        /** @var array[] $results */
        $results = $qb->getQuery()->getArrayResult();

        // If no users were found then permissions did not match.
        if (count($results) === 0) {
            return false;
        }

        // If requireAll is true we need to count the permissions that were found.
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
