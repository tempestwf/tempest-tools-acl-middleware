<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 6/2/2017
 * Time: 5:51 PM
 */

namespace TempestTools\AclMiddleware\Constants;

class PermissionsTemplates{
    const URI = '?{{:frameworkExtracted:route:uri}}';
    const URI_AND_REQUEST_METHOD = '?{{:frameworkExtracted:route:uri}}:{{:frameworkExtracted:request:method}}';
    const ACTION_NAME = '?{{:frameworkExtracted:route:actionName}}';
    const ACTION_NAME_AND_REQUEST_METHOD = '?{{:frameworkExtracted:route:actionName}}:{{:frameworkExtracted:request:method}}';
    const DOMAIN = '?{{:frameworkExtracted:route:domain}}';
    const ENVIRONMENT = '?{{:frameworkExtracted:environment}}';
}