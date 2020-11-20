<?php


/**
 * HTTP Auth - Minimalist authentication to return a JWT
 */
$router
    /**
    * POST deviceDeviceUIDSensorsSensorUIDDataPost
    * Summary: Add data to a sensor
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('POST', '/devices/(\w+)/sensors/(\w+)/data/', function ($deviceUID, $sensorUID) use ($router) {
        
        $data = $router->body;

        $sensor_id = Helper::getSensorID($deviceUID, $sensorUID);
        $qb = new QueryBuilder();
        $count = 0;
        foreach ($data as $item) {
            // Adding the sensors as has-one relation with the device
            $r = $qb
            ->table('data')
            ->fields(['value', 'timestamp', 'sensors_id'])
            ->insert([$item->value, $item->timestamp, $sensor_id]);
            ++$count;
        }
        
        return array('itensIncluded' => $count);

    })

    /**
    * GET deviceDeviceUIDSensorsSensorUIDDataGet
    * Summary: Get data from a sensor
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('GET', '/devices/(\w+)/sensors/(\w+)/data', function ($deviceUID, $sensorUID) use ($router) {
        $from = Helper::getURLGETParameter('from');
        $to = Helper::getURLGETParameter('to');
        
        if (is_null($from) || is_null($to)) 
            throw new HttpException(400, "From e To dates are mandatory");

        $sensor_id = Helper::getSensorID($deviceUID, $sensorUID);
        $qb = new QueryBuilder();
        $r = $qb
            ->table('data')
            ->fields(['value', 'timestamp'])
            ->where(["sensors_id = $sensor_id AND timestamp >= '$from' AND timestamp < '$to'"])
            ->select();
        return $r;
    })

    /**
    * DELETE deviceDeviceUIDSensorsSensorUIDDataDelete
    * Summary: Delete data within the timestamp
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('DELETE', '/devices/(\w+)/sensors/(\w+)/data', function ($deviceUID, $sensorUID) use ($router) {
        $from = Helper::getURLGETParameter('from');
        $to = Helper::getURLGETParameter('to');
        
        if (is_null($from) || is_null($to)) 
            throw new HttpException(400, "From e To dates are mandatory");

        $sensor_id = Helper::getSensorID($deviceUID, $sensorUID);
        $qb = new QueryBuilder();
        $r = $qb
            ->table('data')
            ->where(["sensors_id = $sensor_id AND timestamp >= '$from' AND timestamp < '$to'"])
            ->delete([$sensor_id, $from, $to]);
        return $r;
    });