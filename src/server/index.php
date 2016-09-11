<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: X-Requested-With, content-type');

/**
 * Overwriting of the default __autoload
 */
spl_autoload_register('apiAutoLoad');

function apiAutoLoad($className) {
    if (strpos($className, 'Namshi') === false) {
        try {
            $class = null;

            if (preg_match('/[a-zA-Z]+Controller$/', $className)) {
                $class = __DIR__ . '/controller/' . $className . '.php';
            } elseif (preg_match('/[a-zA-Z]+Model$/', $className)) {
                $class = __DIR__ . '/model/' . $className . '.php';
            } elseif (preg_match('/[a-zA-Z]+View$/', $className)) {
                $class = __DIR__ . '/view/' . $className . '.php';
            } else {
                $class = __DIR__ . '/library/' . $className . '.php';
            }

            if (file_exists($class)) {
                require_once($class);

                return true;
            } else {
                throw new Exception();
            }
        } catch (Exception $e) {
            echo 'Error: Incorrect class name';
        }
    }
}

/**
 * Create the object request from the user request
 */
$request = new Request();
//var_dump($request);

// Check if the request is a valid object request
if ($request->valid) {
    // Detect controller
    $controllerName = ucfirst($request->urlElements[3]) . 'Controller';

    if (class_exists($controllerName)) {

        $controller = new $controllerName();

        // detect action of the controller
        $actionName = strtolower($request->action) . 'Action';
        $result = $controller->$actionName($request);

        // detect view
        $viewName = ucfirst($request->format) . 'View';

        if (class_exists($viewName)) {
            $view = new $viewName();
            $view->render($result);
        }
    }
}