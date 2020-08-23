<?php

/**
 * Step 0: Required frameworks and basic setup
 *
 */

 // Add our Env Vars - my free-hosting doesn't allow .httaccess SetEnv so I use a .php file
require '.env.php';            
// Route handler
require 'router.php';
// Basic ORM
require 'query_builder.php';   
// HttpException
require 'http_exception.php';

// Turn PHP error report on screen on-off
error_reporting(getenv('PHP_ERROR_REPORTING'));
ini_set('display_errors', getenv('PHP_DISPLAY_ERROS'));

// Setting the correct date & time, used in all php timestamps
date_default_timezone_set(getenv('APP_TIME_ZONE'));

/**
 * Step 1: Add the helper classes and route implementation
 *
 */

// PHP file with some data models customizations - used by our ORM
//include 'models.php';

/**
 * Step 2: Helper class with methods required by the application.
 *
 */
include 'helper.php';

/**
 * Step 3: Instantiate our Router application and add routes to it.
 *
 */

$router = new Router;
require 'router_routes_v0.php';

/**
 * Step 4: Run the application.
 *
 * This method should be called last. This executes the Router application
 * and returns the HTTP response to the HTTP client.
 */

try {
    // execute the received method and output the result
    echo $router($router->method(), $router->uri());
}
catch (HttpException $e) {
    // checking for application exceptions/errors and output it as result
    $e->getResponse();
}
