<?php

/**
 * Step 0: Required frameworks and basic setup
 *
 */

 // Add our Env Vars - my free-hosting doesn't allow .httaccess SetEnv so I use a .php file
require '.env.php';            
// HttpException
require 'http_exception.php';
// Route handler
require 'router.php';
// JWT encode e decode methods
require 'jwt_helper.php';
// Basic ORM
require 'query_builder.php';   

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
try {
    $router = new Router;
    $jwt_helper = new JWTHelper;

    // JWT Helper needs a factory to retrieve the tokens
    $jwt_helper->registerTokenFactory(function($kid) use ($router) {
        // Retriving route details from database
        $qb = new QueryBuilder();
        $tokens = $qb
            ->table('tokens')
            ->fields(['secret'])
            ->where(["id = '$kid'"])
            ->select();
        // Each user must have just one token
        if (count($tokens->values) != 1) 
            throw new HttpException(400, "Error finding the user`s token");
        
        return $tokens->values[0]->secret;

    });

    // A middlware to perform JwtAuthorization
    $router->addMiddleware('JwtAuthorization', function() use ($router, $jwt_helper) {
        
        // 'auth' URL use Basic HTTP Auth, so let's skip
        $uri = $router->uri();
        if ($uri == '/auth' || $uri == '/cron') return;

        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {    //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice 
            // side-effect of this fix means we don't care about capitalization 
            // for Authorization)
            $requestHeaders = array_combine(
                array_map('ucwords', 
                array_keys($requestHeaders)), 
                array_values($requestHeaders));

            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        // HEADER: Get the access token from the header
        $token = null;
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                $token = $matches[1];
            }
        }
        if ($token == null) throw new HttpException(401, "No JWT token found");

        // Decode JWT token here
        try {
        	$jwt = $jwt_helper->decode($token, null, true);
        } catch (Exception $e) {
        	throw new HttpException(401, $e->getMessage());
        }

        // Parameters to search for the current route in the DB
        $uriPattern = $router->uriPattern();
        $uriMethod = strtoupper($router->method());
        // Retrieve route details from database
        $qb = new QueryBuilder();
        $routes = $qb
            ->table('routes')
            ->fields(['id'])
            ->where(["route = '$uriPattern' AND ", "verb = '$uriMethod'"])
            ->select();

        // The pattern should match only one route
        if (count($routes->values) != 1) 
            throw new HttpException(400, "Error finding the route parameters");

        $route = $routes->values[0];

        // Checking the route against the list of allowed routes
        if ( ! in_array($route->id, $jwt->allowed_routes )) {
            // user doesn't have access to it
            throw new HttpException(403, 'You are not allowed to call this route');
        }
    });
    
    require 'routes/routes_devices.php';
    require 'routes/routes_sensors.php';
    require 'routes/routes_data.php';
    require 'routes/routes_users.php';
    require 'routes/routes_authorization.php';

    /**
     * Step 4: Run the application.
     *
     * This method should be called last. This executes the Router application
     * and returns the HTTP response to the HTTP client.
     */

    // execute the received method and output the result
    echo $router($router->method(), $router->uri());
}
catch (HttpException $e) {
    // checking for application exceptions/errors and output it as result
    $e->getResponse();
}
