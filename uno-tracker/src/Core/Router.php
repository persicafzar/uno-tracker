<?php

namespace Core;

class Router
{
    private array $routes = [];
    private array $middleware = [];
    private string $prefix = '';

    public function get(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function put(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }

    public function delete(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    public function group(string $prefix, callable $callback, array $middleware = []): void
    {
        $previousPrefix = $this->prefix;
        $previousMiddleware = $this->middleware;

        $this->prefix .= $prefix;
        $this->middleware = array_merge($this->middleware, $middleware);

        $callback($this);

        $this->prefix = $previousPrefix;
        $this->middleware = $previousMiddleware;
    }

    private function addRoute(string $method, string $path, $handler, array $middleware): void
    {
        $fullPath = $this->prefix . $path;
        $fullMiddleware = array_merge($this->middleware, $middleware);

        $this->routes[] = [
            'method'     => $method,
            'path'       => $fullPath,
            'handler'    => $handler,
            'middleware'  => $fullMiddleware,
            'pattern'    => $this->pathToPattern($fullPath),
        ];
    }

    private function pathToPattern(string $path): string
    {
        // تبدیل {param} به regex
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $uri = $request->uri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                // استخراج پارامترها
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // 🆕 اجرای Middleware با ارسال $params
                foreach ($route['middleware'] as $middleware) {
                    $middlewareInstance = new $middleware();
                    
                    // 🆕 بررسی اینکه متد handle چند پارامتر می‌پذیرد
                    $reflection = new \ReflectionMethod($middlewareInstance, 'handle');
                    $paramCount = $reflection->getNumberOfParameters();
                    
                    if ($paramCount >= 2) {
                        // اگر متد ۲ پارامتر می‌پذیرد، $params را هم ارسال کن
                        if (!$middlewareInstance->handle($request, $params)) {
                            return;
                        }
                    } else {
                        // در غیر این صورت فقط $request را ارسال کن
                        if (!$middlewareInstance->handle($request)) {
                            return;
                        }
                    }
                }

                // اجرای Handler
                $this->callHandler($route['handler'], $request, $params);
                return;
            }
        }

        // 404 Not Found
        $this->notFound($request);
    }

    private function callHandler($handler, Request $request, array $params): void
    {
        if (is_string($handler)) {
            // فرمت: "Controller@method"
            [$controller, $method] = explode('@', $handler);
            $controllerInstance = new $controller();
            call_user_func_array([$controllerInstance, $method], [$request, $params]);
        } elseif (is_callable($handler)) {
            call_user_func($handler, $request, $params);
        } elseif (is_array($handler)) {
            [$class, $method] = $handler;
            $instance = new $class();
            call_user_func_array([$instance, $method], [$request, $params]);
        }
    }

    private function notFound(Request $request): void
    {
        $response = new Response();
        
        if ($request->isHtmx() || $request->isAjax()) {
            $response->status(404)->json(['error' => 'Not Found']);
        } else {
            $response->status(404)->html('<h1>404 - Page Not Found</h1>');
        }
    }
}