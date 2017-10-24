<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 6/2/2017
 * Time: 5:51 PM
 */

namespace TempestTools\AclMiddleware\Constants;

use TempestTools\Common\Constants\CommonArrayObjectKeyConstants;

class PermissionsTemplatesConstants{
    const URI = '{{:' . CommonArrayObjectKeyConstants::FRAMEWORK_KEY_NAME . ':route:uri}}';
    const URI_FORCE_FIRST_SLASH = '/{{:' . CommonArrayObjectKeyConstants::FRAMEWORK_KEY_NAME . ':route:uri}}';
    const URI_AND_REQUEST_METHOD = '{{:' . CommonArrayObjectKeyConstants::FRAMEWORK_KEY_NAME . ':route:uri}}:{{:' . CommonArrayObjectKeyConstants::FRAMEWORK_KEY_NAME . ':request:method}}';
    const ACTION_NAME = '{{:' . CommonArrayObjectKeyConstants::FRAMEWORK_KEY_NAME . ':route:actionName}}';
    const ACTION_NAME_AND_REQUEST_METHOD = '{{:' . CommonArrayObjectKeyConstants::FRAMEWORK_KEY_NAME . ':route:actionName}}:{{:' . CommonArrayObjectKeyConstants::FRAMEWORK_KEY_NAME . ':request:method}}';
    const DOMAIN = '{{:' . CommonArrayObjectKeyConstants::FRAMEWORK_KEY_NAME . ':route:domain}}';
    const ENVIRONMENT = '{{:' . CommonArrayObjectKeyConstants::FRAMEWORK_KEY_NAME . ':environment}}';
}