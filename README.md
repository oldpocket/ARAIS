# ARAIS
ARAIS is a Recursive Acronym for an IoT Server

## Motivation ##

Welcome to ARAIS. I start this project as a personal project and decide to publish it in the hope to help other developers with similar necessity. In my free time, I like to play with Arduino/Raspberry Pi/etc and one key point on these kind of solution is to send the data from sensors to somewhere in the internet, where I can read later, create dashboards and so on.

I can find many IoT servers in the internet, many of then as PaaS solutions, offering some kind of free plan... with many limitations. At the same time, as a developer, I could not find a simple and easy IoT server's source code to use as an start point for my projects and avoid the limitations of the free plans.

So I decide to start this project.

## Environment variables: ##
ARAIS need a couple of env. vars to work. This vars can be added inside your .htaccess file, if you have mod_env enabled in your Apache server or equivalent. If you don't have access to it, you can simple add a .env.php file in the root folder, together with your index.php file. Bellow you have an example of this last case, with a list of all Env Vars needed by ARAIS.

Please, be aware that index.php have a line requering .env.php. If you decide to create the vars inside your .htaccess, you need to create an empty .env.php or remove the require inside the index.php.

The ideia behind this env vars is to separe the information that change in differents environment where you run your application (e.g. your localmachine vs your production server).

```php
// Basic Database vars used by connection string
putenv('MYSQL_HOST=[my_arais_mysql_server]:[port]'); // Database Server Host
putenv('MYSQL_DB=[my_arais_db]');       // Database Name
putenv('MYSQL_USER=[my_db_user]');           // Database Username
putenv('MYSQL_PASSWD=[my_db_passwd]');         // Database Password

// Time Zone of the application
putenv('APP_TIME_ZONE=America/Sao_Paulo');

// LOGGING INFORMATION
// SLIM
putenv('SLIM_DEBUG=1');
putenv('SLIM_LOG_ENABLED=1');
// PHP
putenv('PHP_ERROR_REPORTING=E_ALL');
putenv('PHP_DISPLAY_ERROS=1');

// 1 (True) or 0 (False) to use https
putenv('ENABLE_HTTPS=0');
```
