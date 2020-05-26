<?php

/**
 * Start a new Slim application
 */
$app = new \Slim\Slim(array(
	'log.level'   => \Slim\Log::DEBUG, 
	'log.enabled' => getenv('SLIM_LOG_ENABLED'), 
	'log.writer'  => new \Slim\LogWriter(fopen(__DIR__ . '/logs/log-'.date('Y-m-d', time()), 'a')), 
	'debug'       => getenv('SLIM_DEBUG')
));

/**
 * Add a JSON middleware facilitator to be used with Slim
 */
$app->view(new \JsonApiView());
$app->add(new \JsonApiMiddleware());

/**
 * Add Slim authentication middleware
 */
 
// Basic Authentication is used to login and retrieve the JWT token
$app->add(new \Slim\Middleware\HttpBasicAuthentication([
    // Set true to use with https, false otherwise
    'secure' => getenv('ENABLE_HTTPS'),
    // Check user/password against database
    'authenticator' => function ($arguments) use ($app) {
        // Looking for the username in the database
        $token = R::findOne('tokens', 'username = ?', array($arguments['user']));
        // Username not found
        if ($token == NULL) return FALSE;
        // Username found, let's verify against password and return the result
        return password_verify($arguments['password'], $token->password);
    },
    // Because my free-hosting use FastCGI, I need to get authorization header from an env var.
    // Here I set the name of this env var - need to be used in combination with .htaccess
    'environment' => 'HTTP_AUTHORIZATION',
    // Blacklist - URL that must use Basic Authentication
    'path' => '/auth',
    // Error decoding the JWT token. Let's return it to the client
    'error' => function ($arguments) use ($app) {
        // The JSON middleware is not avaliable yet, so we need to produce the 
        // response by hand, but it should be compatible with JSON middleware responses.
        $response['error'] = TRUE;
        $response['status'] = $app->response->getStatus();
        $response['message'] = $arguments['message'];
        $app->response->headers->set('Content-Type', 'application/json');
        $app->response->write(json_encode($response, JSON_UNESCAPED_SLASHES));
    }
]));

// After successfully retrieve a JWT token, all request should be done using it
$app->add(new \Slim\Middleware\JwtAuthentication([
    // Set true to use with https, false otherwise
    'secure' => getenv('ENABLE_HTTPS'),
    // List of secrets
    'secret' => Helper::getTokenSecrets(),
    // Scope of protection
    'realm' => 'Protected',
    // Blacklist - All URL in the list must use JWT Authentication
    'path' => ['/v1', '/jwt'],
    // Whitelist - URL that should not use JWT Authentication
    'passthrough' => ['/auth', '/cron'],
    // Sucess decoding the JWT
    'callback' => function ($options) use ($app) {
        // Decoded JWT will be saved to be used in the $app.
        $app->jwt = $options['decoded'];
    }
]));

/**
 * Define the Slim application routes
 *
 */

// All routes from our IoT APIs
include 'routes_v1.php';
