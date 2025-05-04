<?php

require_once 'Responder.php';

$pattern = __DIR__ . "/../controllers/*.php";
foreach (glob($pattern) as $filename) {

  require_once $filename;
}

class Router
{
  private $get_routes = [];
  private $post_routes = [];

  public function add_post($path, $controller)
  {
    $this->post_routes[$path] = $controller;
  }
  public function add_get($path, $controller)
  {
    $this->get_routes[$path] = $controller;
  }

  public function get()
  {
    self::call($this->get_routes);
  }
  public function post()
  {
    self::call($this->post_routes);
  }

  public function handle()
  {
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'GET') {
      $this->get();
    } elseif ($method === 'POST') {
      $this->post();
    } elseif ($method === 'PUT') {
      Responder::bad_request("Unsupported PUT");
    } elseif ($method === 'DELETE') {
      Responder::bad_request("Unsupported DELETE");
    } else {
      Responder::bad_request("Unknown request " . $method);
    }
  }
  private static function call($router)
  {

    $uri = $_SERVER['REQUEST_URI'];
    $path = parse_url($uri, PHP_URL_PATH);

    $controller = $router[$path] ?? null;
    if ($controller == null) {
      Responder::bad_request("Unknown request");
      return;
    }


    try {
      call_user_func($controller);
    } catch (Throwable $e) {
      Responder::server_error($e->getMessage());
    }
  }
}
