<?php
require_once './backend/domain/User.php';
require_once './backend/core/Token.php';

class UserContoller
{

  // /users/create
  public static function post()
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

    if (User::get_by_email($email)) {
      Responder::error('User already exists', 409);
      return;
    }


    $result = User::create($data);
    if ($result->isOk()) {
      Responder::success(null);
    } else {
      Responder::error('Database error:' . $result->unwrapErr(), 500);
    }
    return;
  }

  // /login
  public static function login()
  {
    $input = file_get_contents(('php://input'));

    $data = json_decode($input, true);

    if ($data === null) {
      Responder::bad_request("invalid login submited");
      return;
    }


    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    //todo: add validation replace login to return a token and not a user

    $user = User::get_by_email($email);
    if ($user === null) {
      Responder::bad_request("user not found");
    } else {
      $token = Token::gen_user_token($user->id);

      if ($token->isOk()) {

        $data = [];
        $data['token'] = $token->unwrap();
        Responder::success(json_encode($data));
        return;
      } else {
        Responder::server_error('Unable to generate auth token');
      }
    }
  }

  // //users/user
  public static function get_user()
  {
    $headers = getallheaders();
    $auth_header = $headers['Authorization'];
    if ($auth_header != null) {
      $token = str_replace('Bearer ', '', $auth_header);


      //todo: add validation replace login to return a token and not a user
      $token_result = Token::get_user_id_from_token($token);


      if (!$token_result->isOk()) {
        Responder::bad_request("Unknown token received");
        return;
      }

      $user = User::get_by_id($token_result->unwrap()['user_id']);

      if ($user === null) {
        Responder::bad_request("user not found");
      } else {
        Responder::success(json_encode($user));
        return;
      }
    } else {
      Responder::bad_request("No authorization token set");
    }
  }

  public static function update()
  {
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
      $auth_header = $_SERVER['HTTP_AUTHORIZATION'];
      $token = str_replace('Bearer ', '', $auth_header);


      $token_result = Token::get_user_id_from_token($token);

      if (!$token_result->isOk()) {
        Responder::bad_request("Unknown token received");
        return;
      }

      $user = User::get_by_id($token_result->unwrap()['user_id']);

      if ($user === null) {
        Responder::bad_request("user not found");
      } else {
        Responder::success(json_encode($user));
        return;
      }
    } else {
      Responder::bad_request("No authorization token");
    }
  }
}
