
# ARAIS
ARAIS is a Recursive Acronym for an IoT Server

## What it is ##
Welcome to ARAIS. ARAIS is an IoT server, written in PHP, using no dependences at all. Everything it needs is part of the code. It use SQLite for data storage and provide a very clean REST collection of APIs, to make it easy to be consumed by small IoT devices.

The collection of APIs can handle device and sensors registration, save and retrieve data from sensors and also the users and permissions management.

It use JWT to garatee the data integrity, and provide authentication and authorization components as well.

The goal of this project is not to replace complex IoT servers already in the market, but provide a lightweight solution for the hobbyist that have some knowledge and skills as a PHP developer.

The final result is so simple and light that I can run it inside my NAS server at home using PHP 5.5 and Lighttpd webserver or in a free hosting server with Apache and lots the restrictions (I run it at AwardSpace with a free account).

## Motivation ##

I start this project as a personal project and decide to publish it in the hope to help other developers with similar requirements. In my free time I like to play with Arduino, Raspberry Pi and other small devices, and one key point on these kind of solution is to send the data from sensors to somewhere in the internet, where I can read it later, create dashboards and so on.

One can find many IoT servers in the internet, huge and complex open sources projects or many of then as PaaS solutions, offering some kind of free plan... with many limitations. As a developer, I could not find a simple and easy IoT server's source code to use as a start point for my projects and avoid the limitations of the free PaaS plans.

So I decide to start this project.

## Installation ##
Quite straight forward. Get the code, copy it to your webserver, update the environment variables from the next section with ones that make sense for your setup and using the ```database.sql``` dump from the repository create a SQLite file called ```data.sqlite```.

The database requirements is quite simple, so it's possible to port to a MySQL database. I have added some env variables in the next section. Small changes in the query db abstraction class will be necessary, but anyone with some PHP background should handle it.

If you find any difficulties, let me know and we can update this document with more steps.

## Important code explanations ##
We use JWT for authorization. That's mean that both users and devices need to be authenticated before they can call APIs. We have a table for users and another for devices. The table token have the encrypted password and token for both. Each also need to have a role in the system (the available roles are in the table role). The access for a given endpoint is controlled in the table roles_route. By default the SQL seed provided create the relationships based on my use case/needs. But you are free to create new roles and allow only specific endpoints to it. For example, one could allow devices to add another devices (in my case, I didn't).

## Environment variables: ##
ARAIS need a couple of env. vars to work. This vars can be added inside your .htaccess file, if you have mod_env enabled in your Apache server or equivalent. If you don't have access to it, you can simple add a .env.php file in the root folder, together with your index.php file. Bellow you have an example of this last case, with a list of all Env Vars needed by ARAIS.

Please, be aware that index.php have a line requiring .env.php. If you decide to create the vars inside your .htaccess, you need to create an empty .env.php or remove the require inside the index.php.

The ideia behind this env vars is to separe the information that change in different environment where you run your application (e.g. your local machine vs your production server).

```php
// SQLite env
putenv('SQLITE_FILE_NAME=data.sqlite');

// MySQL env example
// putenv('MYSQL_HOST=[my_arais_mysql_server]:[port]'); // Database Server Host
// putenv('MYSQL_DB=[my_arais_db]');       // Database Name
// putenv('MYSQL_USER=[my_db_user]');           // Database Username
// putenv('MYSQL_PASSWD=[my_db_passwd]');         // Database Password

// Time Zone of the application
putenv('APP_TIME_ZONE=America/Sao_Paulo');

// LOGGING INFORMATION
// PHP
putenv('PHP_ERROR_REPORTING=E_ALL');
putenv('PHP_DISPLAY_ERROS=1');
```
