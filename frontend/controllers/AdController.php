
<?php

require_once "./frontend/core/View.php";

class AdController
{

  public static function get(string $slug)
  {
    $cookie_name = "ad_slug";
    $cookie_val = $slug;
    setcookie($cookie_name, $cookie_val);
    return Views::get_view('ad');
  }
}
