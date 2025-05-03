<?php
require_once './backend/core/Responder.php';
class ReqRouter
{
  private $routes = [];

  public function add($method, $path, $controller)
  {
    $this->routes[$method][$path] = $controller;
  }

  public function handle($method, $uri)
  {
    $path = parse_url($uri, PHP_URL_PATH);
    $controller = $this->routes[$method][$path] ?? null;

    if (!$controller) {
      Responder::bad_request("Unknown request");
      return;
    }

    [$controller, $function] = $controller;
    (new $controller)->$function();
  }
}

