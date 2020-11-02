<?php


/**
 * HTTP Auth - Minimalist authentication to return a JWT
 */
$router

    /**
    * Get usersGet
    * Summary: Create a new user in the system
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('GET', '/users', function () use ($router) {
    })

    /**
    * Get usersUsernameGet
    * Summary: Create a new user in the system
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('GET', '/users/(\w+)', function ($user) use ($router) {
    })
    
    /**
    * POST usersUsernamePost
    * Summary: Create a new user in the system
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('POST', '/users/(\w+)', function ($user) use ($router) {
    })

    /**
    * PUT usersUsernamePasswordPut
    * Summary: Create a new user in the system
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('PUT', '/users/(\w+)/password', function ($user) use ($router) {
    });