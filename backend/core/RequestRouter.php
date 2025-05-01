<?php 

class ReqRouter {
    private $routes = [];
    
    public function add($method, $path, $controller) {
        $this->routes[$method][$path] = $controller;        
    }
    
    public function handle($method, $uri)  {
        $path = parse_url($uri, PHP_URL_PATH);
        $controller = $this->routes[$method][$path] ?? null; 
        
        if (!$controller) {
            http_response_code(404);
            echo json_encode(['error'=> 'Not Found']);
            return;
        }
        
        [$controller, $function] = $controller;
        (new $class)->$function();
        
    }
        
    
}


?>