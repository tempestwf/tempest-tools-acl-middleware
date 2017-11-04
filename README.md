# Tempest Tools Moat
 
This package allows you to easily place templates which draw on stored information about the user's request to laravel in order to check if a resource is allowed to be accessed by the current user.
 
The package used laravel-doctrine/acl at it’s core, however the functionality that was stored in laravel-doctrine/acl has been replaced due to performance concerns in the base package and errors that package had with phpunit.
 
The base package would retrieve and iterate all permissions available to a user on each request, which if used in an application that has a lot of permissions could be quite a drain on resources. I have replaced this with a simple query.
 
Examples given in the code are based on using the Tempest Tools Skeleton, so the routing examples may not look exactly as you are used to.
 
Please see the wiki for additional documentation.
 
Tempest Tools Moat can be seen in action in the Tempest Tools Skeleton: https://github.com/tempestwf/tempest-tools-skeleton
 
 
 
## Requirements
 
* PHP >= 7.1.0
* laravel/framework = 5.3.*,
 
 
* [Composer](https://getcomposer.org/).
 
##Installation
 
After installing with composer make sure to add the middleware to your Kernel.php file:
\app\Http\Kernel.php
```
  protected $routeMiddleware = [
    'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    'can'        => \Illuminate\Foundation\Http\Middleware\Authorize::class,
    'throttle'   => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    'acl' => \TempestTools\Moat\Http\Middleware\Acl::class
  ];
```
