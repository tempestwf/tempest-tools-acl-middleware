<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 6/2/2017
 * Time: 5:51 PM
 */

namespace TempestTools\AclMiddleware\Constants;

use TempestTools\Common\Laravel\Utility\Extractor;

class PermissionsTemplatesConstants{
    const URI = '?{{:' . Extractor::EXTRACTOR_KEY_NAME . ':route:uri}}';
    const URI_AND_REQUEST_METHOD = '?{{:' . Extractor::EXTRACTOR_KEY_NAME . ':route:uri}}:{{:' . Extractor::EXTRACTOR_KEY_NAME . ':request:method}}';
    const ACTION_NAME = '?{{:' . Extractor::EXTRACTOR_KEY_NAME . ':route:actionName}}';
    const ACTION_NAME_AND_REQUEST_METHOD = '?{{:' . Extractor::EXTRACTOR_KEY_NAME . ':route:actionName}}:{{:' . Extractor::EXTRACTOR_KEY_NAME . ':request:method}}';
    const DOMAIN = '?{{:' . Extractor::EXTRACTOR_KEY_NAME . ':route:domain}}';
    const ENVIRONMENT = '?{{:' . Extractor::EXTRACTOR_KEY_NAME . ':environment}}';
}