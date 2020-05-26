<?php

/**
 * HTTP Auth - Minimalist authentication to return a JWT
 */
$app->get('/auth', function () use ($app) {
	
    $token = R::findOne('tokens', 'username = ?', array($_SERVER['PHP_AUTH_USER']));
    
    // Getting the allowed routes for this user
    $roles_routes = R::findAll('roles_routes', 'roles_id = ?', array($token->roles_id));
    $allowed_routes = array();
    foreach ($roles_routes as $value) {
        
        array_push($allowed_routes, $value->routes_id);
    }
    
    // Preparing the JWT payload
    $timestamp = time();
    $payload = array(
        'username' => $token->username,  // logged in user 
        'allowed_routes' => $allowed_routes,
        'exp' => $timestamp + 6000, // token expiration timeout in seconds
        'iat' => $timestamp
    );
    // ($payload, $secret, algorithm, kid)
    $jwt = \Firebase\JWT\JWT::encode($payload, $token->secret, 'HS256', $token->id);
    	
	$result = ['token' => $jwt];
	$app->render(200, $result);
});

/**
 * My free-hosting does not allow me to have cron jobs, so I have the bellow
 * end point to be called whenever I want to execute a cron job (it can be
 * called from the IFFT online service, for instance.
 */
$app->get('/cron', function () use ($app) {
    Helper::log('cron: ' . date('Y-m-d H:i:s'));
    $app->render(200,array(
        'msg' => date('Y-m-d H:i:s'),
    ));   
});

/**
 * Collection of routes used to manage devices/sensors, data and authorization.
 * All the routes use Helper::authorizeForRoute to check if the caller can 
 * access the given route.
 */
$app->group('/v1', 'Helper::authorizeForRoute', function () use ($app) {

    $app->group('/authorization', function () use ($app) {
        
        /**
        * POST authorizationRolesRoleUIDPost
        * Summary: Create a new role in the system
        * Notes: 
        * Output-Formats: [application/json]
        */
        $app->post('/roles/:roleUID', function($roleUID) use ($app) {
        
            // Checking if we have the role registered
            $role = R::findOne('roles', 'uid = ?', array($roleUID));
            if ($role != NULL) {
                $app->render(409,array(
                    'error' => TRUE,
                    'msg'   => 'Duplicated role UId: ' . $roleUID,
                ));
            }
        
            $data = Helper::textBodytToJSON($app);
            
            $role = R::dispense('roles');
            $role->uid = $roleUID;
            $role->description = $data->description;
            $role_id = R::store($role);
            
            // Return the device created
            $app->render(200,array(
                'roles' => array($role)
            ));
        })->name('authorizationRolesRoleUIDPost');
        
        /**
        * POST authorizationRoutesRouteUIDPost
        * Summary: Register a new route in the system
        * Notes: 
        * Output-Formats: [application/json]
        */
        $app->post('/routes/:routeUID', function($routeUID) use ($app) {
        
            // Checking if we have the route registered
            $route = R::findOne('routes', 'uid = ?', array($routeUID));
            if ($route != NULL) {
                $app->render(409,array(
                    'error' => TRUE,
                    'msg'   => 'Duplicated route UID: ' . $routeUID,
                ));
            }          
        
            $data = Helper::textBodytToJSON($app);
            
            $routes = R::dispense('routes');
            $routes->uid = $routeUID;
            $routes->description = $data->description;
            $routes->route = $data->route;
            $routes->verb = $data->verb;
            $routes_id = R::store($routes);
            
            // Return the device created
            $app->render(200,array(
                'routes' => array($routes)
            ));
        })->name('authorizationRoutesRouteUIDPost'); 
        
        /**
        * POST authorizationPermissionRoleUIDRouteUIDPost
        * Summary: Associate a route with a role
        * Notes: 
        * Output-Formats: [application/json]
        */
        $app->post('/permission/:roleUID/:routeUID', function($roleUID, $routeUID) use ($app) {
        
            $data = Helper::textBodytToJSON($app);
           
            // Checking if we have the role registered
            $role = R::findOne('roles', 'uid = ?', array($roleUID));
            if ($role == NULL) {
                $app->render(404,array(
                    'error' => TRUE,
                    'msg'   => 'Role not found: ' . $roleUID,
                ));
            }
            
            // Checking if we have the route registered
            $route = R::findOne('routes', 'uid = ?', array($routeUID));
            if ($route == NULL) {
                $app->render(404,array(
                    'error' => TRUE,
                    'msg'   => 'Route not found: ' . $routeUID,
                ));
            }  

            $role->sharedRoutesList[] = $route;
            R::store($role);
            
            // Return the device created
            $app->render(200,array(
                
            ));
        })->name('authorizationPermissionRoleUIDRouteUIDPost');          
        
    });

    $app->group('/users', function () use ($app) {
        
        /**
        * POST usersUsernamePost
        * Summary: Create a new user in the system
        * Notes: 
        * Output-Formats: [application/json]
        */
        $app->post('/:username', function($username) use ($app) {
        
            if (strpos($username, 'DEVICE_') === 0) {
                $app->render(400,array(
                    'error' => TRUE,
                    'msg'   => 'Username started with DEVICE_ is not allowed',
                ));
            }
        
            // Checking if we have the username registered
            $route = R::findOne('users', 'username = ?', array($username));
            if ($route != NULL) {
                $app->render(409,array(
                    'error' => TRUE,
                    'msg'   => 'Duplicated username: ' . $username,
                ));
            }  
        
            $data = Helper::textBodytToJSON($app);
            
            $user = R::dispense('users');
            $user->name = $data->name;
            $user->email = $data->email;
            $user_id = R::store($user);
            
            // JWT token secret
            $token = R::dispense('tokens');
            $token->username = $username;
            $token->secret = base64_encode(random_bytes(10));
            $token->password = password_hash($data->password, PASSWORD_DEFAULT);
            $token->ownUsers[] = $user;
            $token_id = R::store($token);
            
            // Saving the role of the user together with the token
            $role = R::findOne('roles', 'uid = ?', array($data->role));
            $role->ownTokens[] = $token;
            R::store($role);
            
            unset($user->password);
            // Return the device created
            $app->render(200,array(
                'users' => array($user)
            ));
            
        })->name('usersUsernamePost');
        
    });

    $app->group('/devices', function () use ($app) {

        /**
        * GET deviceGet
        * Summary: Return device details
        * Notes: 
        * Output-Formats: [application/json]
        */
        $app->get('/', function() use ($app) {
        
            $data = R::findAll('devices');
            
            $return = array();
            foreach ($data as $value) {
                unset($value->id);
                
                array_push($return, $value);
            }
            
            $app->render(200,array(
                'devices' => $return,
            ));   
            
        })->name('deviceGet');
    
        /**
        * POST deviceDeviceUIDPost
        * Summary: Register a new device
        * Notes: 
        * Output-Formats: [application/json]
        */
        $app->post('/:deviceUID', function($deviceUID) use ($app) {
            
            // Checking if we already have the device registered
            Helper::getDevice($deviceUID, FALSE, TRUE);
            
            $data = Helper::textBodytToJSON($app);
            $device_password = base64_encode(random_bytes(5));
            
            // Device UID is unique, lets continue
            $device = R::dispense('devices');
            $device->label = $data->label;
            $device->place = $data->place;


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
                $sensor = R::dispense('sensors');
                $sensor->uid = $data_sensor->uid;
                $sensor->label = $data_sensor->label;
                $device->ownSensors[] = $sensor;
            }
            
            // Store device and it's list of sensors
            $device_id = R::store($device);
            
            // JWT token secret
            $token = R::dispense('tokens');
            $token->username = 'DEVICE_' . $deviceUID;
            $token->secret = base64_encode(random_bytes(10));
			$token->password = password_hash($device_password, PASSWORD_DEFAULT);
            $token->ownDevices[] = $device;
            $token = R::store($token);
            
            // Saving the role of the device together with the token
            $role = R::findOne('roles', 'uid = ?', array('device'));
            $role->ownTokens[] = $token;
            R::store($role);
            
            // Device ID is our database table ID. User cares only about his on
            // UID, so we are removing id from the object before return it in the
            // response
            unset($device->id);
            // Sending the open password for the caller, because right now we have
            // the hashed password in the device object
            $device->password = $device_password;
            
            // Return the device created
            $app->render(200,array(
                'devices' => array($device)
            ));
        })->name('deviceDeviceUIDPost');
        
        /**
        * PUT deviceDeviceUIDPut
        * Summary: Update an existing device
        * Notes: 
        * Output-Formats: [application/json]
        */
        $app->put('/:deviceUID', function($deviceUID) use ($app) {
        
            $app->render(200, array($app->router->getCurrentRoute()->getName()));
        
            // Checking if we have the device registered
            Helper::getDevice($deviceUID, TRUE, FALSE);
            
            $data = Helper::textBodytToJSON($app);
            
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
                $sensor = R::dispense('sensors');
                $sensor->uid = $data_sensor->uid;
                $sensor->label = $data_sensor->label;
                $device->ownSensors[] = $sensor;
            
            }
            
            // Store device and it's list of sensors
            $device_id = R::store($device);
            
            // Device ID is our database table ID. User cares only about his on
            // UID, so we are removing id from the object before return it in the
            // respons
            unset($device->id);
            
            // Return the device with updated information
            $app->render(200,array(
                'devices' => array($device),
            ));
        })->name('deviceDeviceUIDPut');
        
        /**
        * GET deviceDeviceUIDGet
        * Summary: Return device details
        * Notes: 
        * Output-Formats: [application/json]
        */
        $app->get('/:deviceUID', function($deviceUID) use ($app) {
        
            // Checking if we have the device registered
            $device = Helper::getDevice($deviceUID, TRUE, FALSE);
            
            // Device ID is our database table ID. User cares only about his on
            // UID, so we are removing id from the object before return it in the
            // respons
            unset($device->id);
            
            // Return the device
            $app->render(200,array(
                'devices' => array($device),
            ));
        })->name('deviceDeviceUIDGet');
        
        /**
        * DELETE deviceDeviceUIDDelete
        * Summary: Remove a device
        * Notes: 
        * Output-Formats: [application/json]
        */
        $app->delete('/:deviceUID', function($deviceUID) use ($app) {
        
            // Checking if we have the device registered
            $device = Helper::getDevice($deviceUID, TRUE, FALSE);
            
            // Remove all sensors from a device
            $device->xownSensors = array();
            R::store($device);
            
            // Remove the device
            R::trash($device);
        
            // Return the device
            unset($device->id);
            $app->render(200,array(
                'devices' => array($device),
            ));
        })->name('deviceDeviceUIDDelete');
      
        /**
        * GET deviceDeviceUIDSensorsGet
        * Summary: Get information about a sensor
        * Notes: 
        * Output-Formats: [application/json]
        */
        $app->get('/:deviceUID/sensors', function($deviceUID) use ($app) {
        
            // Checking if we have the device registered
            $device = Helper::getDevice($deviceUID, TRUE, FALSE);
            
            $data = R::find('sensors', 'devices_id = ?', [$device->id]);
            
            // Remove unecessary information. We want to return only the sensors ids
            $return = array();
            foreach ($data as $value) {
                unset($value->id);
                unset($value->devices_id);
                
                array_push($return, $value);
            }
            
            $app->render(200,array(
                'sensors' => $return,
            ));            
            
        })->name('deviceDeviceUIDSensorsGet');      
      
        /**
        * POST deviceDeviceUIDSensorsSensorUIDPost
        * Summary: Register a new sensor in an existing device
        * Notes: 
        * Output-Formats: [application/json]
        */
        $app->post('/:deviceUID/sensors/:sensorUID', function($deviceUID, $sensorUID) use ($app) {
        
            $data = Helper::textBodytToJSON();
            
            // Checking if we have the sensor registered with the device
            $sensor = Helper::getSensor($deviceUID, $sensorUID, FALSE, TRUE);
            
            // Device found and sensor not found.
            // Adding the sensors as has-one relation with the device
            $sensor = R::dispense('sensors');
            $sensor->uid = $sensorUID;
            $sensor->label = $data->label;
            
            $device = R::findOne('devices', 'uid = ?', array($deviceUID));
            $device->ownSensors[] = $sensor;
            
            // Store device and it's list of sensors
            R::store($device);
            
            // Return the ID of the new device
            $sensor['device_uid'] = $deviceUID;
            unset($sensor->devices_id);
            unset($sensor->id);
            $app->render(200,array(
                'sensors' => array($sensor),
            ));
        
        })->name('deviceDeviceUIDSensorsSensorUIDPost');

        /**
        * PUT deviceDeviceUIDSensorsSensorUIDPut
        * Summary: Update an existing sensor
        * Notes: 
        * Output-Formats: [application/json]
        */
        $app->put('/:deviceUID/sensors/:sensorUID', function($deviceUID, $sensorUID) use ($app) {
        
            $data = Helper::textBodytToJSON();
            
            // Checking if we have the sensor registered
            $sensor = Helper::getSensor($deviceUID, $sensorUID, TRUE, FALSE);
               
            // Adding the sensors as has-one relation with the device
            $sensor->label = $data->label;
            
            // Store device and it's list of sensors
            R::store($sensor);
            
            // Return the ID of the new device
            unset($sensor->id);
            unset($sensor->devices_id);            
            $sensor['device_uid'] = $deviceUID;
            $app->render(200,array(
                'sensors' => array($sensor),
            ));
            
        })->name('deviceDeviceUIDSensorsSensorUIDPut');

        /**
        * GET deviceDeviceUIDSensorsSensorUIDGet
        * Summary: Return sensor details
        * Notes: 
        * Output-Formats: [application/json]
        */
        $app->get('/:deviceUID/sensors/:sensorUID', function($deviceUID, $sensorUID) use ($app) {
        
            $sensor = Helper::getSensor($deviceUID, $sensorUID, TRUE, FALSE);            
            
            // Return the ID of the new device
            $sensor['device_uid'] = $deviceUID;
            unset($sensor->id);
            unset($sensor->devices_id);            
            $app->render(200,array(
                'error' => FALSE,
                'sensors' => array($sensor),
            ));
            
        })->name('deviceDeviceUIDSensorsSensorUIDGet');
        
        /**
        * DELETE deviceDeviceUIDSensorsSensorUIDDelete
        * Summary: Remove a sensor
        * Notes: 
        * Output-Formats: [application/json]
        */
        $app->delete('/:deviceUID/sensors/:sensorUID', function($deviceUID, $sensorUID) use ($app) {
        
            $sensor = Helper::getSensor($deviceUID, $sensorUID, TRUE, FALSE);
            
            // Remove the sensor
            R::trash($sensor);
            
            // Return the ID of the new device
            $sensor['device_uid'] = $deviceUID;
            unset($sensor->id);
            unset($sensor->devices_id);
            $app->render(200,array(
                'sensors' => array($sensor),
            ));
        
        })->name('deviceDeviceUIDSensorsSensorUIDDelete');
        
        /**
        * POST deviceDeviceUIDSensorsSensorUIDDataPost
        * Summary: Add data to a sensor
        * Notes: 
        * Output-Formats: [application/json]
        */
        $app->post('/:deviceUID/sensors/:sensorUID/data', function($deviceUID, $sensorUID) use ($app) {
        
            $data = Helper::textBodytToJSON();
                    
            $sensor = Helper::getSensor($deviceUID, $sensorUID, TRUE, FALSE);
            
            $count = 0;
            foreach ($data as $value) {
                $data = R::dispense('data');
                $data->value = $value->value;
                $data->timestamp = $value->timestamp;
                $sensor->ownData[] = $data;
                ++$count;
            }
            // Store the stream and associate it with the sensor
            R::store($sensor);
            
            $app->render(200,array(
                'rowsAffected' => $count,
            ));  
        })->name('deviceDeviceUIDSensorsSensorUIDDataPost');
  
        // Store the stream and associate it with the sensor
        /**
        * GET deviceDeviceUIDSensorsSensorUIDDataGet
        * Summary: Get data from a sensor
        * Notes: 
        * Output-Formats: [application/json]
        */
        $app->get('/:deviceUID/sensors/:sensorUID/data', function($deviceUID, $sensorUID) use ($app) {
        
            $sensor = Helper::getSensor($deviceUID, $sensorUID, TRUE, FALSE);
                    
            $data = R::find('data', 'sensors_id = ? AND timestamp >= ? AND timestamp < ?', 
                [
                    $sensor->id, 
                    $app->request->get('from'),
                    $app->request->get('to')
                ]);
            
            $return = array();
            foreach ($data as $value) {
                unset($value->id);
                unset($value->sensors_id);
                array_push($return, $value);
            }
            
            $app->render(200,array(
                'data' => $return,
            ));            
            
        })->name('deviceDeviceUIDSensorsSensorUIDDataGet');
        
        /**
        * DELETE deviceDeviceUIDSensorsSensorUIDDataDelete
        * Summary: Delete data within the timestamp
        * Notes: 
        * Output-Formats: [application/json]
        */
        $app->delete('/:deviceUID/sensors/:sensorUID/data', function($deviceUID, $sensorUID) use ($app) {
            
            $sensor = Helper::getSensor($deviceUID, $sensorUID, TRUE, FALSE);
              
            $deleted = R::hunt('data', 'sensor_id = ? AND timestamp >= ? AND timestamp < ?', 
                [
                    $sensor->id, 
                    $app->request->get('from'),
                    $app->request->get('to')
                ]);
                
            $app->render(200,array(
                'rowsAffected' => $deleted,
            ));   
            
        })->name('deviceDeviceUIDSensorsSensorUIDDataDelete');
        
    });

});
