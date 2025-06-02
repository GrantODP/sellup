
<?php

require_once "./frontend/core/View.php";
require_once "./backend/controllers/AdminController.php";
require_once "./backend/core/Result.php";

class PageAdminController
{



  public static function admin()
  {
    $auth = AdminController::handle_admin();
    if ($auth->isErr()) {
      echo '<h2> Unauthorized</h2>';
      return;
    }

    return Views::get_view('admin.html');
  }
}
