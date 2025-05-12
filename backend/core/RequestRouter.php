<?php

require_once 'Responder.php';

$back_pattern = __DIR__ . "/../controllers/*.php";
$front_pattern = __DIR__ . "/../../frontend/controllers/*.php";
foreach (glob($back_pattern) as $filename) {

  require_once $filename;
}
foreach (glob($front_pattern) as $filename) {

  require_once $filename;
}
class Router
{
  private $get_routes = [];
  private $post_routes = [];
  private $dynamic_get_routes = [];

  public function add_post(string $path, $controller)
  {
    $this->post_routes[$path] = $controller;
  }
  public function add_get(string $path, $controller)
  {
    if (self::is_pattern($path)) {
      $dyn_path = self::convert_to_pattern($path);
      return $this->dynamic_get_routes[$dyn_path] = $controller;
    }

    $this->get_routes[$path] = $controller;
  }

  public function get($path)
  {

    if (self::call($path, $this->get_routes)) {
      return;
    }

    if (self::dynamic_call($path, $this->dynamic_get_routes)) {
      return;
    }
    return Responder::bad_request("Unknown request");
  }
  public function post($path)
  {
    if (self::call($path, $this->post_routes)) {
      return;
    };

    return Responder::bad_request("Unknown request");
  }

  public function handle()
  {
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = $_SERVER['REQUEST_URI'];
    $path = parse_url($uri, PHP_URL_PATH);

    if ($method === 'GET') {
      $this->get($path);
    } elseif ($method === 'POST') {
      $this->post($path);
    } elseif ($method === 'PUT') {
      Responder::bad_request("Unsupported PUT");
    } elseif ($method === 'DELETE') {
      Responder::bad_request("Unsupported DELETE");
    } else {
      Responder::bad_request("Unknown request " . $method);
    }
  }
  private static function call($path, $router): bool
  {

    $controller = $router[$path] ?? null;

    //route not found
    if ($controller == null) {
      return false;
    }
    try {
      call_user_func($controller);
    } catch (Throwable $e) {
      Responder::server_error($e->getMessage());
    }
    return true;
  }

  private static function dynamic_call($path, $router)
  {
    echo 'dynamic_call';

    foreach ($router as $pattern => $controller) {
      echo $pattern;
      if (preg_match($pattern, $path, $matches)) {

        array_shift($matches);
        var_dump($matches);
        try {
          return call_user_func_array($controller, $matches);
        } catch (Throwable $e) {
          Responder::server_error($e->getMessage());
        }
        return true;
      }
      var_dump($matches);
    }
    return false;
  }

  private static function is_pattern($path)
  {
    return strpos($path, '{') !== false || preg_match('/[\(\^]/', $path);
  }

  private static function convert_to_pattern($path)
  {
    $pattern = preg_replace('#\{[^}]+\}#', '([^/]+)', $path);
    return '#^' . $pattern . '$#';
  }
}
