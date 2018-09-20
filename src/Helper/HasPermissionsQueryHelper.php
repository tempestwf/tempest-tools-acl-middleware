<?php

namespace TempestTools\Moat\Helper;

use App\Entities\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use TempestTools\Moat\Contracts\HasPermissionsContract;
use TempestTools\Moat\Contracts\HasRolesContract as HasRolesHasRoles;
use TempestTools\Moat\Contracts\PermissionContract;
use TempestTools\Moat\Contracts\HasIdContract;
use TempestTools\Moat\Exceptions\AclMiddlewareException;

/**
 * A class used to query the database to check for the ACL permissions that are assigned to specific entities.
 *
 * @link    https://github.com/tempestwf
 * @author  William Tempest Wright Ferrer <https://github.com/tempestwf>
 */
class HasPermissionsQueryHelper {

    /**
     * @var string
     */
    protected $roleRelationsName = 'roles';

    /**
     * @var string
     */
    protected $permissionRelationsName = 'permissions';

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * HasPermissionsQueryHelper constructor.
     *
     * @param mixed $repository
     */
    public function __construct($repository) {
        $this->setRepository($repository);
    }


    /**
     * A method that checks if the current entity the trait is applied to has permissions that match the names passed
     *
     * @param HasIdContract $entity
     * @param  array $names
     * @param  bool $requireAll
     * @return bool
     * @throws \RuntimeException
     */
    public function hasPermissionTo(HasIdContract $entity, array $names, bool $requireAll = false) : bool
    {
        $this->checkCompatibility($entity);

        $namesFiltered = $this->prepareNamesArray($names);

        $results = [];
        // If we have the HasPermissionsContract then we know that permissions can be assigned by a Permissions relation
        if ($entity instanceof HasPermissionsContract) {
            $qb = $this->buildHasPermissionToQueryPermissions($entity, $namesFiltered);
            /** @var array[] $results */
            $permissionsResults = $qb->getQuery()->getScalarResult();
            foreach($permissionsResults as $result) {
                $results[] = $result['p_id'];
            }
        }

        // If we have the HasRolesHasRoles then we know that permissions can be assigned by a Roles relation
        if ($entity instanceof HasRolesHasRoles) {
            $qb = $this->buildHasPermissionToQueryRoles($entity, $namesFiltered);
            /** @var array[] $results */
            $rolesResults = $qb->getQuery()->getScalarResult();
            foreach($rolesResults as $result) {
                $results[] = $result['p_id'];
            }
        }



        // If no users were found then permissions did not match.
        if (count($results) === 0) {
            return false;
        }

        // If requireAll is true we need to count the unique permissions that were found.
        if ($requireAll === true) {
            $results = array_unique ($results);
            return count($results) === count($namesFiltered);
        }

        return true;
    }

    /**
     * Builds the query builder query used to test permissions.
     *
     * @param HasIdContract $entity
     * @param array $namesFiltered
     * @return QueryBuilder
     */
    protected function buildHasPermissionToQueryBase(HasIdContract $entity, array $namesFiltered): QueryBuilder
    {
        // We use a query to check if the user has the permissions that are passed rather than using the getRoles and getPermissions methods used previously.
        // This method will be much faster when there are many permissions assigned to the user/role.
        $qb = $this->getRepository()->createQueryBuilder('e');
        $qb->select(['
                partial e.{id}, 
                partial p.{id}
            '])
            ->where($qb->expr()->eq('e.id', ':entityId'))
            ->andWhere($qb->expr()->in('p.name', $namesFiltered));
        $qb->setParameters(['entityId' => $entity->getId()]);

        return $qb;
    }

    /**
     * Builds on the base query to check the permissions table
     *
     * @param HasIdContract $entity
     * @param array $namesFiltered
     * @return QueryBuilder
     */
    protected function buildHasPermissionToQueryPermissions(HasIdContract $entity, array $namesFiltered): QueryBuilder
    {
        $qb = $this->buildHasPermissionToQueryBase( $entity, $namesFiltered);
        $qb->innerJoin('e.' . $this->getPermissionRelationsName(), 'p');
        return $qb;
    }

    /**
     * Builds on the base query to check the roles table and then permissions
     *
     * @param HasIdContract $entity
     * @param array $namesFiltered
     * @return QueryBuilder
     */
    protected function buildHasPermissionToQueryRoles(HasIdContract $entity, array $namesFiltered): QueryBuilder
    {
        $qb = $this->buildHasPermissionToQueryBase( $entity, $namesFiltered);
        $qb->addSelect('partial r.{id}');
        $qb->innerJoin('e.' . $this->getRoleRelationsName(), 'r');
        $qb->innerJoin('r.' . $this->getPermissionRelationsName(), 'p');
        return $qb;
    }

    /**
     * Makes sure the names array is a array, and if it contains Permission objects then we get the names from them.
     * @param $names
     * @return array
     */
    protected function prepareNamesArray($names): array
    {
        $names = (array)$names;
        // If permissions were passed we need to get there names to run our query
        $namesFiltered = [];
        foreach ($names as $key => $value) {
            $namesFiltered[] = $this->getPermissionName($value);
        }
        return $namesFiltered;
    }

    /**
     * Checks that the trait is compatible the class it is applied too
     *
     * @param Entity|HasIdContract $entity
     * @throws \RuntimeException
     */
    protected function checkCompatibility(HasIdContract $entity): void
    {

        if (!$entity instanceof HasPermissionsContract && !$entity instanceof HasRolesHasRoles) {
            throw AclMiddlewareException::needsPermissionContract();
        }

        if (get_class($entity) !== $this->getRepository()->getClassName()) {
            throw AclMiddlewareException::entityMustMatchRepo(get_class($entity), $this->getRepository()->getClassName());
        }

    }

    /**
     * @param PermissionContract|string $permission
     *
     * @return string
     */
    protected function getPermissionName($permission): string
    {
        return $permission instanceof PermissionContract ? $permission->getName() : $permission;
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


    /**
     * @param string $roleRelationsName
     * @return HasPermissionsQueryHelper
     */
    public function setRoleRelationsName(string $roleRelationsName): HasPermissionsQueryHelper
    {
        $this->roleRelationsName = $roleRelationsName;
        return $this;
    }

    /**
     * @param string $permissionRelationsName
     * @return HasPermissionsQueryHelper
     */
    public function setPermissionRelationsName(string $permissionRelationsName): HasPermissionsQueryHelper
    {
        $this->permissionRelationsName = $permissionRelationsName;
        return $this;
    }

    /**
     * @param EntityRepository $repository
     * @return HasPermissionsQueryHelper
     */
    public function setRepository(EntityRepository $repository): HasPermissionsQueryHelper
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * @return EntityRepository
     */
    public function getRepository(): EntityRepository
    {
        return $this->repository;
    }

}
