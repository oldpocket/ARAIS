<?php

/**
 * Provides a collection of usevel methods for our application
 */
class Helper {

    /**
     * Middleware to check if user has access to a given route
     */
    public static function authorizeForRoute() {
        
        $app = \Slim\Slim::getInstance();
        // We need the route UID name to get it's database id
        $routeUID = $app->router->getCurrentRoute()->getName();
        // Retriving route details from database
        $route = R::findOne('routes', 'uid = ?', array($routeUID));
        // Checking the route against the list of allowed routes
        if ( ! in_array($route->id, $app->jwt->allowed_routes )) {
            // user doesn't have access to it
            $app->render(403,array(
                'error' => TRUE,
                'msg'   => 'You are not allowed to call this route',
            ));
        }
        
    }

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

    /**
     * Check if we already have a device with the given UID
     */
    public static function getDevice($deviceUID, $throwNotFound = TRUE, $throwDuplicated = FALSE) {
        
        $app = \Slim\Slim::getInstance();
        
        // Checking if we have the device registered
        $token = R::findOne('tokens', 'entity = ?', array($deviceUID));
        
        // We found the device, but duplicated device UID is not allowed
        if ($token != NULL && $throwDuplicated) {
            $app->render(409,array(
                'error' => TRUE,
                'msg'   => 'Duplicated device UID: ' . $deviceUID,
            ));
        }
        
        // Device not found and we need throw an Exception about it
        if ($token == NULL && $throwNotFound) {
            $app->render(404,array(
                'error' => TRUE,
                'msg'   => 'Device (' . $deviceUID . ') not found',
            ));
        } 
        
        // Return the device if found or null (404 was not required)
        return $token != NULL ? reset( $token->ownDevices ) : NULL;
    }
    
    /**
     * Check if we already have a device with the given UID
     */
    public static function getSensor($deviceUID, $sensorUID, $throwNotFound = TRUE, $throwDuplicated  = FALSE) {
        
        $app = \Slim\Slim::getInstance();
        
        // Checking if we have the device registered
        $device = Helper::getDevice($deviceUID, TRUE, FALSE);

        // Checking if sensor UID is unique for the current device
        // Each sensor should have a unique UID for the Device it's attached
        $sensor = R::findOne('sensors', 
            'uid = ? AND devices_id = ?', 
            array($sensorUID, $device->id)
        );
        
        // We found a duplicated sensor UID in the received list
        if ($sensor != NULL && $throwDuplicated) {
            $app->render(409,array(
                'error' => TRUE,
                'msg'   => 'Duplicated sensor UID(' . $sensorUID . ') for current device',
            ));
        }
        
        // We could not find the sensor and need to throw and Exception about it
        if ($sensor === NULL && $throwNotFound) {
            $app->render(404,array(
                'error' => TRUE,
                'msg'   => 'Sensor ('. $sensorUID .') not found',
            ));
        }
        
        // Return the sensor if found or null (404 was not required)
        return $sensor;
    }

    /**
     * Transform a Slim txt body request in a JSON object
     */
    public static function textBodytToJSON() {
        
        $app = \Slim\Slim::getInstance();
    
        // Decoding JSON body
        $data = json_decode($app->request->getBody());
        if ($data == NULL) {
            // Error parsing the JSON object
            $app->render(400,array(
                'error' => TRUE,
                'msg'   => 'Invalid JSON object',
            ));
        }
        return $data;
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
