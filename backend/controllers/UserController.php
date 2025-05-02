<?php
require_once './backend/domain/User.php';

class UserContoller
{

  // /users
  public function handle_post()
  {

    $input = file_get_contents(('php://input'));

    $data = json_decode($input, true);


    $name = $data['name'] ?? null;
    $password = $data['password'] ?? null;
    $email = $data['email'] ?? null;
    $contact = $data['contact'] ?? null;

    if (
      empty($name) ||
      empty($password) ||
      empty($email) ||
      empty($contact)
    ) {
      Responder::error("Invalid input", 400); // 400 Bad Request
      return;
    }

    if (User::exists($email)) {
      Responder::error('User already exists', 409);
      return;
    }

    $user = new User($name, $email, $contact, $password);
    $result = $user->post();
    if ($result->isOk()) {
      Responder::success(null);
    } else {
      Responder::error('Database error:' . $result->unwrapErr(), 500);
    }
    return;
  }
}

