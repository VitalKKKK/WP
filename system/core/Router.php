<?php

class Router 
{
    static function load() {
        $controller = 'Main';
        $action = 'index';

        $routes = explode('/', $_SERVER['REQUEST_URI']);

        if (!empty($routes[1])) {
            $controller = $routes[1];
        }
        
        if ( !empty($routes[2]) ) {
            $action = $routes[2];
        }

		$controller = ucfirst(strtolower($controller)) . 'Controller';
        $controller_file = $controller . '.php';
        $controller_path = "application/controllers/".$controller_file;//DIRECTORY_SEPARATOR
        if( file_exists($controller_path)) {
            include "application/controllers/".$controller_file;
        } else {
            echo 'Controller not found';
            exit;
        }
        
        $class = new $controller;

        if(method_exists($class, $action)) {
            $class->$action();
        } else {
            echo 'Controller action not found';
            exit;
        }

    }
}

