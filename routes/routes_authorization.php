<?php

/**
 * Routes to manage authorization actions involving roles/rotes and permissions
 */
$router

    /**
    * POST authorizationRolesRoleUIDPost
    * Summary: Create a new role in the system
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('POST', '/authorization/roles/(\w+)', function ($roleUID) use ($router) {
        $qb = new QueryBuilder();
        $r = $qb
            ->table('roles')
            ->fields(['id'])
            ->where(["uid = '$roleUID'"])
            ->select();
        if (count($r->values) > 0) 
            throw new HttpException(409, "Duplicated role UID: $roleUID");
        
        $data = $router->body;
        $role_id = $qb
            ->table('roles')
            ->fields(['uid', 'description'])
            ->insert([$roleUID, $data->description]);
        
        $role = $qb
            ->table('roles')
            ->fields(['uid', 'description'])
            ->where(["id = '$role_id'"])
            ->select();

        return $role;

    })
    
    /**
    * POST authorizationRoutesRouteUIDPost
    * Summary: Register a new route in the system
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('POST', '/authorization/routes/(\w+)', function ($routeUID) use ($router) {
        $qb = new QueryBuilder();
        $r = $qb
            ->table('routes')
            ->fields(['id'])
            ->where(["uid = '$routeUID'"])
            ->select();
        if (count($r->values) > 0) 
            throw new HttpException(409, "Duplicated route UID: $routeUID");
        
        $data = $router->body;
        $route_id = $qb
            ->table('routes')
            ->fields(['uid', 'description', 'route', 'verb'])
            ->insert([$routeUID, $data->description, $data->route, $data->verb]);
        
        $route = $qb
            ->table('routes')
            ->fields(['uid', 'description', 'route', 'verb'])
            ->where(["id = '$route_id'"])
            ->select();

        return $route;
    })
    
    /**
    * POST authorizationPermissionRoleUIDRouteUIDPost
    * Summary: Associate a route with a role
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('POST', '/authorization/permission/(\w+)/(\w+)', function ($roleUID, $routeUID) use ($router) {
        $qb = new QueryBuilder();
        $route = $qb
            ->table('routes')
            ->fields(['id'])
            ->where(["uid = '$routeUID'"])
            ->select();
        if (count($route->values) == 0) 
            throw new HttpException(404, "Route UID not found: $routeUID");
        $route_id = $route->values[0]->id;

        $role = $qb
            ->table('roles')
            ->fields(['id'])
            ->where(["uid = '$roleUID'"])
            ->select();
        if (count($role->values) == 0) 
            throw new HttpException(404, "Role UID not found: $roleUID");
        $role_id = $role->values[0]->id;

        $roles_routes_id = $qb
            ->table('roles_routes')
            ->fields(['routes_id', 'roles_id'])
            ->insert([$route_id, $role_id]);

		return array('included' => 1);
    });
