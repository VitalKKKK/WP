<?php

class Router 
{
    static function load() {
        $controller = 'Main';
        $action = 'index';

        $routes = preg_replace("/\?(.*)/", "", $_SERVER['REQUEST_URI']);
        $routes = explode(DIRECTORY_SEPARATOR, $routes);

        if (!empty($routes[1])) {
            $controller = $routes[1];
        }
        if ( !empty($routes[2]) ) {
            $action = $routes[2];
        }

		$controller = ucfirst(strtolower($controller)) . 'Controller';
        $controller_file = $controller . '.php';
        $controller_path = APPLICATION_PATH . "controllers". DIRECTORY_SEPARATOR . $controller_file;
        if( file_exists($controller_path)) {
            include $controller_path;
        } else {
            echo 'Controller not found';
            exit;
        }
        
        $class = new $controller;

        if(method_exists($class, $action)) {
            $class->$action();
//            echo $controller . $action;
        } else {
            echo 'Controller action not found';
            exit;
        }

    }
}

