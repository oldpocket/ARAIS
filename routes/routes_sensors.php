<?php


/**
 * Routes to manage sensors. A sensor is part of an IoT device and later send data to
 * be store.
 */
$router
    /**
    * GET deviceDeviceUIDSensorsGet
    * Summary: Get information about a sensor
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('GET', '/devices/(\w+)/sensors/', function ($deviceUID) {
      
        $device_id = Helper::getDeviceID($deviceUID);
        $qb = new QueryBuilder();
        $r = $qb
            ->table('sensors')
            ->fields(['uid', 'label', 'created', 'modified'])
            ->where(["devices_id = $device_id"])
            ->select();

        return $r;
      
    })

    /**
    * GET deviceDeviceUIDSensorsSensorUIDGet
    * Summary: Return sensor details
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('GET', '/devices/(\w+)/sensors/(\w+)/', function ($device, $sensor) {
        $qb = new QueryBuilder();
        $r = $qb
            ->table('devices')
            ->fields(['id'])
            ->where(["uid = $device"])
            ->select();
        if (count($r->values) == 0) 
            throw new HttpException(404, "Device not found");

        $device_id = $r->values[0]->id;
        $r = $qb
            ->table('sensors')
            ->fields(['uid', 'label', 'created', 'modified'])
            ->where(["devices_id = $device_id AND uid = $sensor"])
            ->select();
        if (count($r->values) == 0) 
            throw new HttpException(404, "Sensor not found");

        return $r;
      
    })

    /**
    * DELETE deviceDeviceUIDSensorsSensorUIDDelete
    * Summary: Remove a sensor
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('DELETE', '/devices/(\w+)/sensors/(\w+)/', function ($device, $sensor) {
        $qb = new QueryBuilder();
        $r = $qb
            ->table('devices')
            ->fields(['id'])
            ->where(["uid = $device"])
            ->select();
        if (count($r->values) == 0) 
            throw new HttpException(404, "Device not found");

        $device_id = $r->values[0]->id;
        $r = $qb
            ->table('sensors')
            ->where(["devices_id = $device_id AND uid = $sensor"])
            ->delete([$device]);

        return array('deleted' => $r);

    })

    /**
    * POST deviceDeviceUIDSensorsSensorUIDPost
    * Summary: Register a new sensor in an existing device
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('POST', '/devices/(\w+)/sensors/(\w+)/', function ($device, $sensor) use ($router) {
        $data = $router->body;

        $qb = new QueryBuilder();
        $r = $qb
            ->table('devices')
            ->fields(['id'])
            ->where(["uid = $device"])
            ->select();
        if (count($r->values) == 0) 
            throw new HttpException(404, "Device not found");

        $device_id = $r->values[0]->id;

        $r = $qb
            ->table('sensors')
            ->fields(['uid'])
            ->where(["devices_id = $device_id AND uid = $sensor"])
            ->select();
        if (count($r->values) > 0) 
            throw new HttpException(409, "Duplicated sensor UID: $sensor, for Device: $device");

        // Adding the sensors as has-one relation with the device
        $now = date('Y-m-d H:i:s');
        $r = $qb
            ->table('sensors')
            ->fields(['uid', 'label', 'devices_id', 'modified', 'created'])
            ->insert([$sensor, $data->label, $device_id, $now, $now]);

        // Getting the new object and return it
        $r = $qb
            ->table('sensors')
            ->fields(['uid', 'label', 'devices_id', 'modified', 'created'])
            ->where(["id = '$r'"])
            ->select();

        return $r;
    })

    /**
    * PUT deviceDeviceUIDSensorsSensorUIDPut
    * Summary: Update an existing sensor
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('PUT', '/devices/(\w+)/sensors/(\w+)/', function ($device, $sensor) use ($router) {
        $data = $router->body;

        $qb = new QueryBuilder();
        $r = $qb
            ->table('devices')
            ->fields(['id'])
            ->where(["uid = $device"])
            ->select();
        if (count($r->values) == 0) 
            throw new HttpException(404, "Device not found");

        $device_id = $r->values[0]->id;

        $r = $qb
            ->table('sensors')
            ->fields(['uid'])
            ->where(["devices_id = $device_id AND uid = $sensor"])
            ->select();
        if (count($r->values) == 0) 
            throw new HttpException(404, "Sensor not found: $sensor");

        // Adding the sensors as has-one relation with the device
        $now = date('Y-m-d H:i:s');
        $r = $qb
            ->table('sensors')
            ->fields(['label', 'modified'])
            ->where(["devices_id = $device_id AND uid = $sensor"])
            ->update([$data->label, $now]);

		return array('updated' => $r);
    });
