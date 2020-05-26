<?php

/**
 * Step 0: Required frameworks and basic setup
 *
 */

// Composer initialization
require 'vendor/autoload.php';
// ORM framework - ReadBean doesn't recomment use via composer
require 'RedBeanPHP/rb.php';   
// Add our Env Vars - my free-hosting doesn't allow .httaccess SetEnv so I use a .php file
require '.env.php';            

// Turn PHP error report on screen on-off
error_reporting(getenv('PHP_ERROR_REPORTING'));
ini_set('display_errors', getenv('PHP_DISPLAY_ERROS'));

// Setting the correct date & time, used in all php timestamps
date_default_timezone_set(getenv('APP_TIME_ZONE'));

/**
 * Step 1: Setup and initialize RedBeanPHP - ORM framework
 *
 */

// Database setup and basic connection
R::setup(
    'mysql:host=' . getenv('MYSQL_HOST') . ';dbname=' . getenv('MYSQL_DB'), 
    getenv('MYSQL_USER'), 
    getenv('MYSQL_PASSWD')
);

// PHP file with some data models customizations - used by RedBean
include 'models.php';

/**
 * Step 2: Helper class with methods required by the application
 *
 */
include 'helper.php';

/**
 * Step 3: Instantiate our Slim application
 *
 */
include 'slim_app.php';
 
/**
 * Step 3: Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();
