<?php

namespace Core;

class Router
{

    protected $routes = [];

    protected $params = [];



    public function add($route, $params = [])
    {

         // Convert the route to a regular expression: escape forward slashes
         $route = preg_replace('/\//', '\\/', $route);

         // Convert variables e.g. {controller}
         $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);
 
         // Convert variables with custom regular expressions e.g. {id:\d+}
         $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);
 
         // Add start and end delimiters, and case insensitive flag
         $route = '/^' . $route . '$/i';
 
         $this->routes[$route] = $params;
     
    }


    // public function match($url){

    //     foreach ($this->routes as $route => $params) {
    //         if ($url == $route) {
    //             $this->params = $params;
    //             return true;
    //         }
    //     }
    //     return false;

    // }
    

    public function match($url)
    {
    //$reg_exp = "/^(?P<controller>[a-z-]+)\/(?P<action>[a-z-]+)/";
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                // Get named capture group values
             //   $params = [];
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }

                $this->params = $params;
                return true;
            }
        }

        return false;
    }

    public function getParams()
    {

        return  $this->params;
    }
    public function getRoutes()
    {

        return  $this->routes;
    }


    public function dispatch($url)
    {
        //ejemplo posts/index?page=1&view=2
        $url = $this->removeQueryStringVariables($url);

        if ($this->match($url)) {
            $controller = $this->params['controller'];
            $controller = $this->convertToStudlyCaps($controller);
            $controller = $this->getNamespace() . $controller;

            if (class_exists($controller)) {
                $controller_object = new $controller($this->params);

                $action = $this->params['action'];
                $action = $this->convertToCamelCase($action);

                if (is_callable([$controller_object, $action])) {
                    $controller_object->$action();

                } else {
                    throw new \Exception("Method $action (in controller $controller) not found");
                }
            } else {
                throw new \Exception("Controller class $controller not found");
            }
        } else {
            throw new \Exception('No route matched.', 404);
          
        }
    }

    /**
     * Convert the string with hyphens to StudlyCaps,
     * e.g. post-authors => PostAuthors
     *
     * @param string $string The string to convert
     *
     * @return string
     */
    protected function convertToStudlyCaps($string)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    /**
     * Convert the string with hyphens to camelCase,
     * e.g. add-new => addNew
     *
     * @param string $string The string to convert
     *
     * @return string
     */
    protected function convertToCamelCase($string)
    {
        return lcfirst($this->convertToStudlyCaps($string));
    }

    protected function removeQueryStringVariables($url)
    {
        if ($url != '') {
            $parts = explode('&', $url, 2);

            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }

        return $url;
    }

   
    protected function getNamespace()
    {
        $namespace = 'App\Controllers\\';
//para acceder a subdirectorios de controlladores
        if (array_key_exists('namespace', $this->params)) {
            $namespace .= $this->params['namespace'] . '\\';
        }

        return $namespace;
    }

}
