<?php


/**
 * HTTP Auth - Minimalist authentication to return a JWT
 */
$router
    ->on('GET', '/auth', function () {
        
        $auth_user = $_SERVER['PHP_AUTH_USER'];
        
        if (!$auth_user) 
            throw new HttpException(400, "No user informed");
        
        $qb = new QueryBuilder();
        
        // Looking for the user token
        $tokens = $qb
            ->table('tokens')
            ->fields(['id', 'roles_id', 'secret', 'username'])
            ->where(["username = '$auth_user'"])
            ->select();
        // Each user must have just one token
        if (count($tokens->values) != 1) 
            throw new HttpException(400, "Duplicated username in the system");

        $token = $tokens->values[0];

        // Getting the allowed routes for this user
        $roles_id = $token->roles_id;
        $roles_routes = $qb
            ->table('roles_routes')
            ->fields(['routes_id'])
            ->where(["roles_id = '$roles_id'"])
            ->select();

        $allowed_routes = array();
        foreach ($roles_routes->values as $value) {
            array_push($allowed_routes, $value->routes_id);
        }

        // All user should have at least one route, otherwise he/she/it doesnt 
        // have what to do in the system
        if (count($allowed_routes) == 0) 
            throw new HttpException(400, "No routes allowed for this user");
        
        // Preparing user's JWT payload
        $timestamp = time();
        $payload = array(
            'username' => $token->username,  // logged in user 
            'allowed_routes' => $allowed_routes,
            'exp' => $timestamp + 6000, // token expiration timeout in seconds
            'iat' => $timestamp
        );

        // ($payload, $secret, algorithm)
        $jwt = JWTHelper::encode($payload, $token->secret, $token->id,'HS256');
        $result = ['token' => $jwt];

        // Returning the JWT
        return $result;
    })

    ->on('GET', '/devices/(\w+)/', function ($device) {
        
        $qb = new QueryBuilder();
        $r = $qb
            ->table('data')
            ->fields(['sensor_uid', 'timestamp', 'value'])
            ->where(["device_uid = '$device'"])
            ->select();
        return json_encode($r);

    });
