<?php


/**
 * Routes to manage devices in the system. A device is an IoT hardware that can have
 * many sensors inside.
 */
$router
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
            ->select();
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
            ->fields(['id'])
            ->where(["uid = $device"])
            ->select();
        if (count($r->values) == 0) 
            throw new HttpException(404, "Device not found");
        
        $r = $qb
            ->table('devices')
            ->where(['uid = $device'])
            ->delete([$device]);

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
            ->select();
        if (count($r->values) == 0) 
            throw new HttpException(404, "Device not found");

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
            ->select();

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

        // Getting the role for a 'device'. A role for a device is pre-populated in the
        // seed of the table. Avoid to fix the value here in case I change in the future.
        $role = $qb
            ->table('roles')
            ->fields(['id'])
            ->where(["uid = 'device'"])
            ->select();
        if (count($role->values) == 0) 
            throw new HttpException(404, "Role for device not found");
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
        
        $now = date('Y-m-d H:i:s');
        // Device UID is unique, lets continue to insert
        $device_id = $qb
            ->table('devices')
            ->fields(['uid', 'label', 'place', 'tokens_id', 'created', 'modified'])
            ->insert([$device, $data->label, $data->place, $token_id, $now, $now]);

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
                ->fields(['uid', 'label', 'devices_id', 'modified', 'created'])
                ->insert([$data_sensor->uid, $data_sensor->label, $device_id, $now, $now]);
        }
        
        // Returning the created device
        $device = $qb
            ->table('devices')
            ->fields(['label', 'place', 'created', 'modified', 'uid'])
            ->where(["id = $device_id"])
            ->select();
        $device->values[0]->password = $device_password;

        return $device;

    });
