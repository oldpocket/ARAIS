<?php

/**
 * HTTP Auth - Minimalist authentication to return a JWT
 */
$router

    /**
    * POST authorizationRolesRoleUIDPost
    * Summary: Create a new role in the system
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('POST', '/authorization/roles/(\w+)', function ($roleUID) use ($router) {
    })
    
    /**
    * POST authorizationRoutesRouteUIDPost
    * Summary: Register a new route in the system
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('POST', '/authorization/routes/(\w+)', function ($roteUID) use ($router) {
    })
    
    /**
    * POST authorizationPermissionRoleUIDRouteUIDPost
    * Summary: Associate a route with a role
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('POST', '/authorization/permission/(\w+)/(\w+)', function ($roleUID, $routeUID) use ($router) {
    });