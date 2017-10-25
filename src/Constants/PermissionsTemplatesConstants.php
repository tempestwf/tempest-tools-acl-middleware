<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 6/2/2017
 * Time: 5:51 PM
 */

namespace TempestTools\AclMiddleware\Constants;

use TempestTools\Common\Constants\CommonArrayObjectKeyConstants;

/**
 * Constants used as templates in array expressions related to ACL permissions. Templates will read from data pulled by a framework extractor.
 *
 * @link    https://github.com/tempestwf
 * @author  William Tempest Wright Ferrer <https://github.com/tempestwf>
 */
class PermissionsTemplatesConstants{
    /**
     * URI of the request
     */
    const URI = '{{:' . CommonArrayObjectKeyConstants::FRAMEWORK_KEY_NAME . ':route:uri}}';
    /**
     * URI of the request adding a slash at the front. Dingo with Laravel proves unreliable as to whether or not a proceeding slash is added it to the URIs it detects from the request. This template lets you compensate for that unreliability
     */
    const URI_FORCE_FIRST_SLASH = '/{{:' . CommonArrayObjectKeyConstants::FRAMEWORK_KEY_NAME . ':route:uri}}';
    /**
     * URI of the request with a ':' method of the request appended to it
     */
    const URI_AND_REQUEST_METHOD = '{{:' . CommonArrayObjectKeyConstants::FRAMEWORK_KEY_NAME . ':route:uri}}:{{:' . CommonArrayObjectKeyConstants::FRAMEWORK_KEY_NAME . ':request:method}}';
    /**
     * The controller action of the request
     */
    const ACTION_NAME = '{{:' . CommonArrayObjectKeyConstants::FRAMEWORK_KEY_NAME . ':route:actionName}}';
    /**
     * The controller action of the request with a ':' method of the request appended to it
     */
    const ACTION_NAME_AND_REQUEST_METHOD = '{{:' . CommonArrayObjectKeyConstants::FRAMEWORK_KEY_NAME . ':route:actionName}}:{{:' . CommonArrayObjectKeyConstants::FRAMEWORK_KEY_NAME . ':request:method}}';
    /**
     * The domain name of the request
     */
    const DOMAIN = '{{:' . CommonArrayObjectKeyConstants::FRAMEWORK_KEY_NAME . ':route:domain}}';
    /**
     * Environment name of the application
     */
    const ENVIRONMENT = '{{:' . CommonArrayObjectKeyConstants::FRAMEWORK_KEY_NAME . ':environment}}';
}