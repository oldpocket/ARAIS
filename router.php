<?php
/**
* Router - main class responsible to handle all the registered routes in the system
*
* The code was based on:
* https://phpzm.rocks/php-like-a-boss-3-construa-seu-router-e024ea32ee8a
* and handle all the router methods.
* Documentation using DockBlocks.
* 
* @author      Fabio Andreozzi Godoy <fabio.godoy@oldpocket.com>
* @copyright   2019-2020 Fabio Godoy
* @link        https://github.com/oldpocket/ARAIS
* @license     https://github.com/oldpocket/ARAIS/blob/master/LICENSE
* @version     0.5.0
* @package     ARAIS
*
* @method Router get($route, $callable)
* @method Router post($route, $callable)
* @method Router put($route, $callable)
* @method Router delete($route, $callable)
* 
*/
class Router {

    /** @var array $routes Store all the registered routes for the system */
    private $routes = [];
    
    /** @var string $body Contains the request body if avaliable */
    public $body = '';
                
    /**
     * Check the content-type of the request. It must by of type application/json
     * 
     * @return void
     */
    private function checkContentType() {
        $type = '';
        // GET and DELETE must accept JSON response
        // They don't have content-type (both have empty bodys)
        // so we are checking only Accept header.
        if (in_array($this->method(), array('get', 'delete'))) {
            $type = $this->getHeaderList()['Accept'];
        }
        // POST and PUT must send content-type in JSON format
        // If they can send content-type JSON, we assume they can receive
        // content-type JSON. So we are not checking Accept header here.
        if (in_array($this->method(), array('post', 'put'))) {
            $type = $this->getHeaderList()['Content-Type'];
        }
        // Both Accept and Content-Type must be JSON
        if (strcmp($type, 'application/json') !== 0) {
            http_response_code(400);
            exit();
        }
    }
    
    /**
     * Get a list of headers from the request
     * 
     * @return array $headerList The array if a list of found headers
     */
    private function getHeaderList() {
        // Create an array to put our header info into.
        $headerList = array();
        // Loop through the $_SERVER superglobals array.
        foreach ($_SERVER as $name => $value) {
            // If the name starts with HTTP_, it's a request header.
            if (preg_match('/^HTTP_/',$name) || preg_match('/CONTENT_TYPE/',$name)) {
                //convert HTTP_HEADER_NAME to the typical "Header-Name" format.
                $name = preg_match('/^HTTP_/',$name) ? strtr(substr($name,5), '_', ' ') : strtr($name, '_', ' ');
                $name = ucwords(strtolower($name));
                $name = strtr($name, ' ', '-');
                //Add the header to our array.
                $headerList[$name] = $value;
            }
        }
        //Return the array.
        return $headerList;
    }
    
    public function __construct() {
        // Check if we are receiving the right content-type and if the client
        // accept our JSON response.
        $this->checkContentType();
        // Saving the body for future use
        $this->body = json_decode(file_get_contents('php://input'), false);
    }
    
    /**
     * Return the HTTP verb of the request
     * 
     * @return string The HTTP verb or cli if it's been called from command line
     */
    public function method() {
        return isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : 'cli';
    }

    /**
     * Return the request URI
     *
     * @return string $uri The cleaned up request URI 
     */
    public function uri() {
        $self = isset($_SERVER['PHP_SELF']) ? str_replace('index.php/', '', $_SERVER['PHP_SELF']) : '';
        $uri = isset($_SERVER['REQUEST_URI']) ? explode('?', $_SERVER['REQUEST_URI'])[0] : '';

        if ($self !== $uri) {
            $peaces = explode('/', $self);
            array_pop($peaces);
            $start = implode('/', $peaces);
            $search = '/' . preg_quote($start, '/') . '/';
            $uri = preg_replace($search, '', $uri, 1);
        }

        return $uri;
    }

    /**
    * @param $method The HTTP verb
    * @param $path The URI that is being called
    * @param $callback The function callback that will handle the set Verb+Path
    * @return string $this The result of the callback execution
    */
    public function on($method, $path, $callback) {
        $method = strtolower($method);
        if (!isset($this->routes[$method])) {
            $this->routes[$method] = [];
        }

        $uri = substr($path, 0, 1) !== '/' ? '/' . $path : $path;
        $pattern = str_replace('/', '\/', $uri);
        $route = '/^' . $pattern . '$/';

        $this->routes[$method][$route] = $callback;

        return $this;
    }

    /**
    * The __invoke method is called when a script tries to call an object as a function.
    *
    * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.invoke
    *
    * @param $method
    * @param $uri
    * @return mixed
    */
    public function __invoke($method, $uri) {
        return $this->run($method, $uri);
    }

    /**
    * @param $method
    * @param $uri
    * @return mixed|null
    */
    public function run($method, $uri) {
        
        $method = strtolower($method);
        if (!isset($this->routes[$method])) {
            return null;
        }
        
        foreach ($this->routes[$method] as $route => $callback) {
            if (preg_match($route, $uri, $parameters)) {
                array_shift($parameters);
                header('Content-Type: application/json');
                return $this->call($callback, $parameters);
            }
        }
        return null;
    }

    /**
    * @param $callback
    * @param $parameters
    * @return mixed
    */
    public function call($callback, $parameters) {
        if (is_callable($callback)) {
            return call_user_func_array($callback, $parameters);
        }
        return null;
    }
}
