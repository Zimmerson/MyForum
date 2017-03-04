<?php

namespace App;

use App\Exceptions\RouteNotFoundException;
use App\Exceptions\Router\MissingParameterException;

/**
 * Class Router
 *
 * This class is responsible for taking a URL and finding an appropriate controller and action to run.
 *
 * @package App
 */
class Router
{
    /** @var string The namespace to the controllers. */
    private $controllerPrefix = 'App\\Controllers';

    /** @var null|string The name of the found controller or null if not found. */
    private $controllerClass = null;

    /** @var null|string The name of the controller action or null if not found. */
    private $controllerAction = null;

    /** @var string The path to the pages directory. */
    private $pagePrefix = '../pages';

    /** @var null|string The file path to the template for the controller and action combination. */
    private $page = null;

    /**
     * Router constructor.
     *
     * @param string $route The route to parse.
     */
    public function __construct(string $route)
    {
        $this->parseRoute($route);
    }

    /**
     * Parse the provided route and locate the controller class we can use to process it.
     *
     * @param string $route The route to parse.
     * @throws RouteNotFoundException Thrown when the route cannot be determined.
     */
    public function parseRoute(string $route)
    {
        $route = trim($route, '/');

        if (strpos($route, '?') >= 0) {
            // We don't care about query parameters here, we have $_GET for that.
            $route = explode('?', $route)[0];
        }

        $pathParts = array_filter(explode('/', $route));

        /*
         * We have to make two checks as the following could be true for admin/permissions
         *  - AdminController::permissions()
         *  - admin/PermissionsController::index()
         * We will check the top option first.
         */

        // This allows us to reduce various checks later.
        while (count($pathParts) < 2) {
            $pathParts[] = 'index';
        }

        $controllerAction = array_pop($pathParts);
        $controllerName = array_pop($pathParts);

        // Any remaining parts will form the path to the controller.
        $controllerPath = implode('\\', $pathParts);

        $isValidTypeOne = $this->isValidControllerAndAction($controllerPath, $controllerName, $controllerAction);
        $isValidTypeTwo = $this->isValidControllerAndAction("$controllerPath\\$controllerName", $controllerAction, 'index');

        if (!$isValidTypeOne && !$isValidTypeTwo) {
            throw new RouteNotFoundException("No Controller and Action combination found for $route");
        }

        if ($isValidTypeOne) {
            $this->controllerClass = $this->getFullControllerClass($controllerPath, $controllerName);
            $this->controllerAction = $controllerAction;
            $this->page = $this->getRouteTemplate($controllerPath, $controllerName, $controllerAction);
        } elseif ($isValidTypeTwo) {
            $this->controllerClass = $this->getFullControllerClass("$controllerPath\\$controllerName", $controllerAction);
            $this->controllerAction = 'index';
            $this->page = $this->getRouteTemplate("$controllerPath\\$controllerName", $controllerAction, 'index');
        }
    }

    /**
     * Check to see if the controller exists and the action valid.
     *
     * @param string $path The path to the controller file.
     * @param string $controller The name of the controller.
     * @param string $action The method to run on the controller.
     * @return bool True if the route is valid; false otherwise.
     */
    private function isValidControllerAndAction(string $path, string $controller, string $action)
    {
        $controllerClass = $this->getFullControllerClass($path, $controller);

        if (!class_exists($controllerClass)) {
            return false;
        }

        if (!method_exists($controllerClass, $action)) {
            return false;
        }

        return true;
    }

    /**
     * Get the template file for this route.
     *
     * @param string $path The path to the controller file.
     * @param string $controller The name of the controller.
     * @param string $action The method to run on the controller.
     * @return string The path to the template file.
     */
    private function getRouteTemplate(string $path, string $controller, string $action)
    {
        // Convert the namespace format to file path format.
        $path = str_replace('\\', '/', $path);

        $parts = array_filter([$this->pagePrefix, $path, $controller, $action]);

        return implode('/', $parts) . '.php';
    }

    /**
     * Get the full name spaced class using the class prefix, path, and name.
     *
     * @param string $path The path to the controller file.
     * @param string $name The name of the controller.
     * @return string A fully qualified class name.
     */
    private function getFullControllerClass(string $path, string $name)
    {
        $name = ucfirst($name) . 'Controller';
        $path = trim($path, '\\');

        $parts = [];

        if (strlen($this->controllerPrefix)) {
            $parts[] = $this->controllerPrefix;
        }

        if (strlen($path)) {
            $parts[] = $path;
        }

        $parts[] = $name;

        return implode('\\', $parts);
    }

    /**
     * Get the controller for the current route.
     *
     * @return Controller|null Controller if one matches the provided route; null otherwise.
     */
    public function getControllerObject()
    {
        if ($this->controllerClass) {
            return new $this->controllerClass;
        }

        return null;
    }

    /**
     * Get the file path to the template this route will use.
     *
     * @return string The file path to the template file.
     */
    public function getPageFile()
    {
        return $this->page;
    }

    /**
     * Run the action on the found controller and return the controller.
     *
     * @return Controller The controller object for the parsed route.
     * @throws \Exception Thrown when a controller action is missing arguments.
     */
    public function dispatch()
    {
        // Figure out if there are any parameters our controller action requires.
        // We will pass $_REQUEST variables with the same name automatically.
        $reflectionMethod = new \ReflectionMethod($this->controllerClass, $this->controllerAction);
        $parameters = $reflectionMethod->getParameters();

        // The list of arguments we will pass to the action.
        $arguments = [];

        foreach ($parameters as $parameter) {
            if (isset($_REQUEST[$parameter->getName()])) {
                $arguments[] = $_REQUEST[$parameter->getName()];
            } else {
                if ($parameter->isOptional()) {
                    $arguments[] = $parameter->getDefaultValue();
                } else {
                    throw new MissingParameterException($this->controllerClass, $this->controllerAction, $parameter);
                }
            }
        }

        $controllerObject = $this->getControllerObject();

        if ($arguments) {
            $controllerObject->{$this->controllerAction}(...$arguments);
        } else {
            $controllerObject->{$this->controllerAction}();
        }

        // The controller object contains data our view will need.
        return $controllerObject;
    }
}
