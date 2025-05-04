<?php
require_once './backend/domain/User.php';
require_once './backend/core/Token.php';;
require_once './backend/core/Authorizer.php';;
require_once './backend/util/Util.php';

class UserController
{

  // POST /users/create
  public static function post()
  {


    $data = get_input_json();

    if (!has_required_keys($data, ['name', 'password', 'email', 'contact'])) {
      Responder::bad_request("Invalid input");
      return;
    }

    $email = $data['email'];

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

  // POST /login
  public static function login()
  {
    $data = get_input_json();
    if ($data === null) {
      Responder::bad_request("invalid login submited");
      return;
    }


    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    //todo: add password validation

    $user = User::get_by_email($email);

    if ($user === null) {
      Responder::unauthorized("Invalid credentials, user not found");
    }

    $token = Tokener::gen_user_token($user->id);

    if (!$token->isOk()) {
      Responder::server_error('Unable to generate auth token');
      return;
    }

    $data = [];
    $data['token'] = $token->unwrap();
    Responder::success($data);
  }

  // GET //user
  public static function get_user()
  {
    $auth_token = Authorizer::validate_token_header();

    if (!$auth_token->is_valid()) {
      return Responder::unauthorized($auth_token->message());
    }

    $user = User::get_by_id($auth_token->user_id());

    if ($user === null) {
      Responder::bad_request("user not found");
    } else {
      Responder::success($user);
      return;
    }
  }

  // POST /user/update
  public static function update()
  {

    $auth_token = Authorizer::validate_token_header();

    if (!$auth_token->is_valid()) {
      return Responder::bad_request($auth_token->message());
    }

    $user = User::get_by_id($auth_token->user_id());

    if ($user === null) {
      Responder::bad_request("user not found");
    } else {
      Responder::success($user);
    }
  }
}
