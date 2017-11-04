<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 8/19/2017
 * Time: 6:52 PM
 */

namespace TempestTools\Moat\Exceptions;


/**
 * Exception for ACL middleware
 *
 * @link    https://github.com/tempestwf
 * @author  William Tempest Wright Ferrer <https://github.com/tempestwf>
 */
class AclMiddlewareException extends \RunTimeException
{
    /**
     * @return AclMiddlewareException
     */
    public static function hasPermissionsOptimizedTraitMustBeAppliedToEntity (): AclMiddlewareException
    {
        return new self ('Error: HasPermissionsOptimizedTrait must be applied to an entity');
    }

    /**
     * @return AclMiddlewareException
     */
    public static function needsGetIdError (): AclMiddlewareException
    {
        return new self ('Error: HasPermissionsQueryTrait trait must be used on an entity with a getId method.');
    }

    /**
     * @return AclMiddlewareException
     */
    public static function needsPermissionContract (): AclMiddlewareException
    {
        return new self ('Error: entity must implement either: HasPermissionsContract or HasRolesHasRoles to use the HasPermissionsQueryTrait trait');
    }

    /**
     * @param string $entityName
     * @param string $className
     * @return AclMiddlewareException
     */
    public static function entityMustMatchRepo (string $entityName, string $className): AclMiddlewareException
    {
        return new self (sprintf('Error: entity must match the repo it was passed to to use the HasPermissionsQueryHelper. Entity class name = %s, expected class name = %s.', $entityName, $className));
    }


}



