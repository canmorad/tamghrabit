<?php
namespace App\Core;

class Router
{
    private static $router = null;
    private $routes = [];
    private $namedRoutes = [];
    private $lastRegistedRoute = null;
    private $tempMiddlewares = [];
    private $groupMiddleware = [];

    protected $middlewareMap = [
        'auth' => \App\Middleware\AuthMiddleware::class,
        'guest' => \App\Middleware\GuestMiddleware::class,
        'admin' => \App\Middleware\AdminMiddleware::class,
        'adherent' => \App\Middleware\AdherentMiddleware::class,
    ];

    public static function getRouter()
    {
        if (!self::$router) {
            self::$router = new Router();
        }
        return self::$router;
    }

    public function get($uri, $action)
    {
        return $this->register($uri, $action, "GET");
    }

    public function post($uri, $action)
    {
        return $this->register($uri, $action, "POST");
    }

    public function put($uri, $action)
    {
        return $this->register($uri, $action, "PUT");
    }

    public function delete($uri, $action)
    {
        return $this->register($uri, $action, "DELETE");
    }

    private function register($uri, $action, $method)
    {
        if (!isset($this->routes[$method])) {
            $this->routes[$method] = [];
        }

        $this->routes[$method][$uri] = [
            'controller' => $action[0],
            'method' => $action[1],
            'middleware' => $this->groupMiddleware
        ];

        $this->lastRegistedRoute = [
            'method' => $method,
            'uri' => $uri
        ];

        return $this;
    }

    public function name($name)
    {
        if ($this->lastRegistedRoute) {
            $method = $this->lastRegistedRoute['method'];
            $uri = $this->lastRegistedRoute['uri'];
            $this->namedRoutes[$name] = $uri;
        }
        return $this;
    }

    public function route($name)
    {
        if (!isset($this->namedRoutes[$name])) {
            $this->abort("Route {$name} non trouvée", 404);
        }
        return $this->namedRoutes[$name];
    }

    public function group($callback)
    {

        $previousGroupMiddleware = $this->groupMiddleware;
        $this->groupMiddleware = array_merge($this->groupMiddleware, $this->tempMiddlewares);
        $this->tempMiddlewares = []; 

        $callback($this);

        $this->groupMiddleware = $previousGroupMiddleware;
    }

    public function middleware($keys)
    {
        $keys = is_array($keys) ? $keys : [$keys];

        if ($this->lastRegistedRoute === null || !empty($this->tempMiddlewares)) {
            $this->tempMiddlewares = $keys;
        } else {
            $method = $this->lastRegistedRoute["method"];
            $uri = $this->lastRegistedRoute["uri"];
            foreach ($keys as $key) {
                if (!in_array($key, $this->routes[$method][$uri]['middleware'])) {
                    $this->routes[$method][$uri]['middleware'][] = $key;
                }
            }
        }
        return $this;
    }

    public function dispatch($method, $uri)
    {
        if (!isset($this->routes[$method])) {
            $this->abort("Méthode HTTP non supportée", 405);
        }

        $route = $this->routes[$method][$uri];

        if (!$route) {
            $this->abort("Route non trouvée", 404);
        }

        if (!empty($route['middleware'])) {
            foreach ($route['middleware'] as $key) {
                $middlewareClass = $this->middlewareMap[$key];
                (new $middlewareClass())->handle();
            }
        }

        $controller = $route['controller'];
        $function = $route['method'];

        if (!class_exists($controller)) {
            $this->abort("Controller {$controller} introuvable", 500);
        }

        $controllerInstance = new $controller();

        if (!method_exists($controllerInstance, $function)) {
            $this->abort("Aucune méthode {$function} n'existe dans {$controller}", 500);
        }

        $controllerInstance->$function();

        return true;
    }

    public function abort($message, $code = 404)
    {
        http_response_code($code);
        echo $message;
        exit();
    }
}
