<?php

namespace TempestTools\AclMiddleware\Permissions;

use App\Entities\Entity;
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
    protected $needsGetIdError = 'Error: HasPermissionsOptimized trait must be applied to a entity that implements a getId method';

    /**
     * @var string
     */
    protected $needsEntityBaseClass = 'Error: class must extend App\Entities\Entity to use the HasPermissionsOptimized trait';

    /**
     * @var string
     */
    protected $roleRelationsName = 'roles';

    /**
     * @var string
     */
    protected $permissionRelationsName = 'permissions';

    /**
     * A method that checks if the current entity the trait is applied to has permissions that match the names passed
     * @param  array $names
     * @param  bool $requireAll
     * @return bool
     * @throws \RuntimeException
     */
    public function hasPermissionTo($names, $requireAll = false) : bool
    {
        // If you can't get the id from the entity then this trait is not compatible with the class
        if (!method_exists ($this, 'getId')) {
            throw new RuntimeException($this->getNeedsGetIdError());
        }

        if (!is_subclass_of($this, Entity::class)) {
            throw new RuntimeException($this->getNeedsEntityBaseClass());
        }

        // If permissions were passed we need to get there names to run our query
        $namesFiltered = [];
        foreach ($names as $key => $value) {
            $namesFiltered[] = $this->getPermissionName($value);
        }

        // We use a query to check if the user has the permissions that are passed rather than using the getRoles and getPermissions methods used previously.
        // This method will be much faster when there are many permissions assigned to the user/role.
        /** @var $em EntityManager */
        $em = \App::make(EntityManager::class);
        $qb = $em->createQueryBuilder();
        $qb->select(['e.id'])
            ->from(static::class, 'e')
            ->where(
                $qb->expr()->eq('e.id', $this->getId())
            );

        $wheres = [];
        // If we have the HasPermissionsContract then we know that permissions can be assigned by a Permissions relation
        if ($this instanceof HasPermissionsContract) {
            $qb->leftJoin('e.' . $this->getPermissionRelationsName(), 'p');
            $wheres[] = $qb->expr()->in('p.name', $namesFiltered);
        }

        // If we have the HasRolesHasRoles then we know that permissions can be assigned by a Roles relation
        if ($this instanceof HasRolesHasRoles) {
            $qb->leftJoin('e.' . $this->getRoleRelationsName(), 'r');
            $qb->leftJoin('e.' . $this->getPermissionRelationsName(), 'p2');
            $wheres[] = $qb->expr()->in('p2.name', $namesFiltered);
        }
        // Add the wheres for either roles or permissions or both depending which contracts were present.
        $orX = call_user_func_array([$qb->expr(), 'orX'], $wheres);
        $qb->andWhere(
            $orX
        );

        /** @var array[] $results */
        $results = $qb->getQuery()->getArrayResult();

        // If no users were found then permissions did not match.
        if (count($results) === 0) {
            return false;
        }

        // If requireAll is true we need to count the permissions that were found. We need to find the unique permissions to count in case they came from both relations used.
        if ($requireAll === true) {
            $matchedPermissions = [];
            if (array_key_exists('Permissions', $results)) {
                array_merge($matchedPermissions, $results['Permissions']);
            }
            if (array_key_exists('Roles', $results)) {
                foreach ($results['Roles'] as $key => $value) {
                    if (array_key_exists('Permissions', $value)) {
                        array_merge($matchedPermissions, $value['Permissions']);
                    }
                }
            }
            $matchedPermissions = array_unique ($matchedPermissions);
            return count($matchedPermissions) === count($namesFiltered);
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

    /**
     * @return string
     */
    public function getNeedsEntityBaseClass(): string
    {
        return $this->needsEntityBaseClass;
    }

    /**
     * @return string
     */
    public function getRoleRelationsName(): string
    {
        return $this->roleRelationsName;
    }

    /**
     * @return string
     */
    public function getPermissionRelationsName(): string
    {
        return $this->permissionRelationsName;
    }
}
