<?php


/**
 * HTTP Auth - Minimalist authentication to return a JWT
 */
$router
    /**
     * HTTP Auth - Minimalist authentication to return a JWT
     */
    ->on('GET', '/auth', function () {
        
        $auth_user = $_SERVER['PHP_AUTH_USER'];
        $auth_passwd = $_SERVER['PHP_AUTH_PW'];
        
        if (!$auth_user) 
            throw new HttpException(400, "No user informed");
        
        $qb = new QueryBuilder();
        
        // Looking for the user token
        $tokens = $qb
            ->table('tokens')
            ->fields(['id', 'roles_id', 'secret', 'password', 'username'])
            ->where(["username = '$auth_user'"])
            ->select();
        // Each user must have just one token
        if (count($tokens->values) != 1) 
            throw new HttpException(400, "Duplicated username in the system");

        $token = $tokens->values[0];
        if ($token == NULL || !password_verify($auth_passwd, $token->password))
            throw new HttpException(401, 'Username or password does not match');

        // Getting the allowed routes for this user
        $roles_id = $token->roles_id;
        $roles_routes = $qb
            ->table('roles_routes')
            ->fields(['routes_id'])
            ->where(["roles_id = $roles_id"])
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

    /**
    * GET deviceGet
    * Summary: Return device details
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('GET', '/devices/', function () {
        
        $qb = new QueryBuilder();
        $r = $qb
            ->table('devices')
            ->fields(['uid', 'created', 'label', 'place'])
            ->select();
        if (count($r->values) == 0) 
            throw new HttpException(404, "No device found");

        return $r;
    })

    /**
    * GET deviceDeviceUIDGet
    * Summary: Return device details
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('GET', '/devices/(\w+)/', function ($device) {
        
        $qb = new QueryBuilder();
        $r = $qb
            ->table('devices')
            ->fields(['uid', 'created', 'label', 'place'])
            ->where(["uid = $device"])
            ->selectOne();
        if (count($r->values) == 0) 
            throw new HttpException(404, "Device not found");

        return $r;

    })

    /**
    * DELETE deviceDeviceUIDDelete
    * Summary: Remove a device
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('DELETE', '/devices/(\w+)/', function ($device) {
        
        $qb = new QueryBuilder();
        $r = $qb
            ->table('devices')
            ->where(['uid = $device'])
            ->delete([$device]);
        if (count($r->values) == 0) 
            throw new HttpException(404, "Device not found");

        return $r;

    })

    /**
    * PUT deviceDeviceUIDPut
    * Summary: Update an existing device
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('PUT', '/devices/(\w+)/', function ($device) use ($router) {
        
        // Collecting data
        $data = $router->body;
        $now = date('Y-m-d H:i:s');

        // Looking for the device
        $qb = new QueryBuilder();
        $r = $qb
            ->table('devices')
            ->fields(['*'])
            ->where(["uid = '$device'"])
            ->selectOne();

        // Device UID was found, lets update it
        $r = $qb
            ->table('devices')
            ->fields(['label', 'place', 'last_ip', 'modified'])
            ->where(["uid = '$device'"])
            ->update([$data->label, $data->place, $data->last_ip, $now]);

        // Getting the new object and return it
        $r = $qb
            ->table('devices')
            ->fields(['label', 'place', 'modified', 'uid', 'last_ip'])
            ->where(["uid = '$device'"])
            ->selectOne();

        return $r;

    })
    
    /**
    * POST deviceDeviceUIDPost
    * Summary: Register a new device
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('POST', '/devices/(\w+)/', function ($device) use ($router) {
        
        $qb = new QueryBuilder();
        $r = $qb
            ->table('devices')
            ->fields(['id'])
            ->where(["uid = '$device'"])
            ->select();
        if (count($r->values) > 0) 
            throw new HttpException(409, "Duplicated device UID: $device");
        
        $data = $router->body;
        $device_password = base64_encode(random_bytes(5));

        // Saving the role of the device together with the token
        $role = $qb
            ->table('roles')
            ->fields(['id'])
            ->where(["uid = 'device'"])
            ->selectOne();
        $role_id = $role->values[0]->id;

        // JWT token secret
        $token_id = $qb
            ->table('tokens')
            ->fields(['username', 'secret', 'password', 'roles_id'])
            ->insert([
                'DEVICE_' . $device,
                base64_encode(random_bytes(10)), 
                password_hash($device_password, PASSWORD_DEFAULT), 
                $role_id]);

        // Device UID is unique, lets continue
        $device_id = $qb
            ->table('devices')
            ->fields(['uid', 'label', 'place', 'tokens_id'])
            ->insert([$device, $data->label, $data->place, $token_id]);

        $sensor_uids = array(); // used to store a list of sensors uid
        foreach ($data->sensors as $data_sensor) {
            
            // Checking if sensor UID is unique for the current device
            // Each sensor should have a unique UID for the Device it's attached
            if (in_array($data_sensor->uid, $sensor_uids)) {
                // We found a duplicated sensor UID in the received list
                $app->render(409,array(
                    'error' => TRUE,
                    'msg'   => 'Duplicated sensor UID(' . $data_sensor->uid . ') for current device',
                ));
            }
            array_push($sensor_uids, $data_sensor->uid);
            
            // Adding the sensors as has-one relation with the device
            $sensor_id = $qb
                ->table('sensors')
                ->fields(['uid', 'label', 'devices_id'])
                ->insert([$data_sensor->uid, $data_sensor->label, $device_id]);
        }
        
        // Returning the created device
        $device = $qb
            ->table('devices')
            ->fields(['label', 'place', 'created', 'modified', 'uid'])
            ->where(["id = $device_id"])
            ->selectOne();
        $device->values[0]->password = $device_password;

        return $device;

    });
