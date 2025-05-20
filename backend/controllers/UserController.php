<?php
require_once './backend/domain/User.php';
require_once './backend/domain/Message.php';
require_once './backend/core/Token.php';;
require_once './backend/core/Authorizer.php';;
require_once './backend/util/Util.php';

class UserController
{

  // POST /user/create
  public static function post()
  {


    $data = get_input_json();

    if (!has_required_keys($data, ['name', 'password', 'email', 'contact'])) {
      Responder::bad_request("Invalid input");
      return;
    }

    $email = trim($data['email']);

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
    if (empty($data)) {
      Responder::bad_request("invalid login submited");
      return;
    }

    if (!has_required_keys($data, ['email', 'password'])) {
      Responder::bad_request("Invalid input");
      return;
    }
    $email = trim($data['email']);
    $password = trim($data['password']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return Responder::bad_request($email . " is not a valid email");
    }

    //todo: add password validation
    $user = User::get_by_email($email);

    if ($user === null) {
      Responder::unauthorized("Invalid credentials, user not found");
    }

    $token = Tokener::get_token($user->id);
    if ($token->isErr()) {
      $token = Tokener::gen_user_token($user->id);
    }

    if ($token->isErr()) {
      Responder::server_error('Unable to generate auth token');
      return;
    }

    setcookie(
      'auth_token',
      $token->unwrap(),
      [
        'expires' => time() + 3600, // 1 hour
        'path' => '/',
        'secure' => true,     // Only send over HTTPS
        'httponly' => true,   // Inaccessible to JavaScript
        'samesite' => 'Strict' // Prevent CSRF
      ]
    );
  }

  // GET /user
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

  //todo
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


  // POST /user/message
  public static function send_message()
  {

    $data = get_input_json();

    $auth_token = Authorizer::validate_token_header();

    if (!$auth_token->is_valid()) {
      return Responder::bad_request($auth_token->message());
    }


    if (!has_required_keys($data, ['receiver', 'message'])) {
      Responder::bad_request("Invalid input");
      return;
    }


    $data['sender_id'] = $auth_token->user_id();

    $message = new Message($data);
    $result = $message->post();
    if ($result->isErr()) {
      Responder::server_error('Failed sending message: ' . $result->unwrapErr());
      return;
    }
    return Responder::success();
  }


  // POST /user/message-seller
  public static function message_seller()
  {

    $data = get_input_json();

    $auth_token = Authorizer::validate_token_header();

    if (!$auth_token->is_valid()) {
      return Responder::bad_request($auth_token->message());
    }


    if (!has_required_keys($data, ['receiver', 'message'])) {
      Responder::bad_request("Invalid input");
      return;
    }


    $data['sender_id'] = $auth_token->user_id();
    $user_id = Seller::get_user_id($data['receiver']);

    if ($user_id === null) {
      return Responder::not_found("No seller by id: " . $data['receiver']);
    }

    $data['receiver'] = $user_id;

    $message = new Message($data);
    $result = $message->post();
    if ($result->isErr()) {
      Responder::server_error('Failed sending message: ' . $result->unwrapErr());
      return;
    }
    return Responder::success();
  }
}
