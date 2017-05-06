<?php

namespace TempestTools\AclMiddleware\Helper;

use App\Entities\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use LaravelDoctrine\ACL\Contracts\HasPermissions as HasPermissionsContract;
use LaravelDoctrine\ACL\Contracts\HasRoles as HasRolesHasRoles;
use LaravelDoctrine\ACL\Contracts\Permission as PermissionContract;
use RuntimeException;
use TempestTools\Common\Utility\ErrorConstantsTrait;

class HasPermissionsQueryHelper {
    use ErrorConstantsTrait;
    /**
     * @var array ERRORS
     * A constant that stores the errors that can be returned by the class
     */
    const ERRORS = [
        'needsGetIdError'=>
            [
                'message'=>'Error: HasPermissionsQueryTrait trait must be used on an entity with a getId method.'
            ],
        'needsPermissionContract'=>
            [
                'message'=>'Error: entity must implement either: HasPermissionsContract or HasRolesHasRoles to use the HasPermissionsQueryTrait trait'
            ],
        'entityMustMatchRepo'=>
            [
                'message'=>'Error: entity must match the repo it was passed to to use the HasPermissionsQueryHelper'
            ]
    ];


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
     * @param EntityRepository $repository
     */
    public function __construct(EntityRepository $repository) {
        $this->setRepository($repository);
    }


    /**
     * A method that checks if the current entity the trait is applied to has permissions that match the names passed
     *
     * @param Entity $entity
     * @param  array $names
     * @param  bool $requireAll
     * @return bool
     * @throws \RuntimeException
     */
    public function hasPermissionTo(Entity $entity, $names, $requireAll = false) : bool
    {
        // Make sure that trait is compatible with class it is applied too
        $this->checkCompatibility($entity);

        // Prepare the names array
        $namesFiltered = $this->prepareNamesArray($names);

        $qb = $this->buildHasPermissionToQuery($entity, $namesFiltered);
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
     * Builds the query builder query used to test permissions.
     *
     * @param Entity $entity
     * @param array $namesFiltered
     * @return QueryBuilder
     */
    protected function buildHasPermissionToQuery(Entity $entity, array $namesFiltered): QueryBuilder
    {
        // We use a query to check if the user has the permissions that are passed rather than using the getRoles and getPermissions methods used previously.
        // This method will be much faster when there are many permissions assigned to the user/role.
        $qb = $this->getRepository()->createQueryBuilder('e');
        $qb->select(['
                partial e.{id},
                partial r.{id}, 
                partial p.{id},
                partial p2.{id}
            '])
            ->where(
                $qb->expr()->eq('e.id', $entity->getId())
            );

        $wheres = [];
        // If we have the HasPermissionsContract then we know that permissions can be assigned by a Permissions relation
        if ($entity instanceof HasPermissionsContract) {
            $qb->leftJoin('e.' . $this->getPermissionRelationsName(), 'p');
            $wheres[] = $qb->expr()->in('p.name', $namesFiltered);
        }

        // If we have the HasRolesHasRoles then we know that permissions can be assigned by a Roles relation
        if ($entity instanceof HasRolesHasRoles) {
            $qb->leftJoin('e.' . $this->getRoleRelationsName(), 'r');
            $qb->leftJoin('r.' . $this->getPermissionRelationsName(), 'p2');
            $wheres[] = $qb->expr()->in('p2.name', $namesFiltered);
        }
        // Add the wheres for either roles or permissions or both depending which contracts were present.
        $orX = call_user_func_array([$qb->expr(), 'orX'], $wheres);
        $qb->andWhere(
            $orX
        );
        return $qb;
    }

    /**
     * Make sure the names array is a array, and if it contains Permission objects then we get the names from them.
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
     * @param Entity $entity
     * @throws \RuntimeException
     */
    protected function checkCompatibility(Entity $entity) {
        // If you can't get the id from the entity then this trait is not compatible with the class
        if (!method_exists ($entity, 'getId')) {
            throw new RuntimeException($this->getErrorFromConstant('needsGetIdError')['message']);
        }

        if (!$entity instanceof HasPermissionsContract && !$entity instanceof HasRolesHasRoles) {
            throw new RuntimeException($this->getErrorFromConstant('needsPermissionContract')['message']);
        }

        if (get_class($entity) !== $this->getRepository()->getClassName()) {
            throw new RuntimeException($this->getErrorFromConstant('entityMustMatchRepo')['message']);
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
