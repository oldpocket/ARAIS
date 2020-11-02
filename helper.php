<?php

/**
 * Provides a collection of usevel methods for our application
 */
class Helper {

    /**
     * Get a list of token from the database in the array format
     * necessary for the JWT midlleware
     */
    public static function getTokenSecrets() {
        $response = R::findAll('tokens');
        $result = array();
        foreach ($response as $row)
        { 
            $result[$row['id']] = $row['secret'];
        }
        return $result;
    }

    /*
     * Get the URL Get Query Parameter or null
     */
    public static function getURLGETParameter($name) {
        return (isset($_GET[$name])) ? urldecode($_GET[$name]) : null;
    }

    /**
     * Retrieve the device ID based on the UID
     */
    public static function getDeviceID($deviceUID) {
        $qb = new QueryBuilder();
        $r = $qb
            ->table('devices')
            ->fields(['id'])
            ->where(["uid = $deviceUID"])
            ->selectOne();
        if (count($r->values) == 0) 
            throw new HttpException(404, "Device not found");

        return $r->values[0]->id;
    }
    
    /**
     * Retrieve the sensor ID based on the UID and it's device UID
     */
    public static function getSensorID($deviceUID, $sensorUID) {
        $device_id = self::getDeviceID($deviceUID);
        $qb = new QueryBuilder();
        $r = $qb
            ->table('sensors')
            ->fields(['id'])
            ->where(["devices_id = $device_id AND uid = $sensorUID"])
            ->selectOne();
        if (count($r->values) == 0) 
            throw new HttpException(404, "Sensor not found");
        
        return $r->values[0]->id;
    }

    /**
     * Transform a Slim txt body request in a JSON object
     */
    public static function textBodytToJSON() {
        
    
    }
    
    /**
     * Local LOG function
     */
    public static function log($message) {
        try {
            $log = R::dispense('log');
            $log->timestamp = date('Y-m-d H:i:s');
            $log->calling = self::getCallingFunctionName(2);
            $log->message = $message;
            R::store($log);
        } catch (Exception $e) {
            $data = $message.PHP_EOL;
            $fp = fopen('exception_log.txt', 'a');
            fwrite($fp, $data);
        }
    }
    
    /**
     * Get the name of the last called function. $level tell us if we want the last
     * parent caller, or the last-last parent caller, so on...
     * $level = 0, is the same function
     * $level = 1, is the function before
     * $level = 2, is the function before before
     * $level = 3, ... etc.
     */
    private static function getCallingFunctionName($level) { 
      
        // Create an exception 
        $ex = new Exception(); 
        
        // Call getTrace() function 
        $trace = $ex->getTrace(); 
        
        // Position 0 would be the line 
        // that called this function 
        $final_call = $trace[$level]; 
        
        // Display associative array  
        return print_r($final_call, true); 
    }
}
