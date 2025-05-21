<?php
require_once './backend/domain/User.php';
require_once './backend/domain/Listing.php';
require_once './backend/domain/Cart.php';
require_once './backend/domain/Order.php';
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
    $password = trim($data['password']);

    if (User::get_by_email($email)) {
      Responder::error(message: 'User already exists', status: 409);
      return;
    }


    $result = User::create($data);

    if ($result->isErr()) {
      return Responder::error('Error:' . $result->unwrapErr(), 500);
    }

    $result_auth = Authorizer::store_validation($result->unwrap(), $password);
    if ($result_auth->isErr()) {
      return Responder::error('Error:' . $result_auth->unwrapErr(), 500);
    }

    return Responder::success(null);
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

    $user = User::get_by_email($email);

    if (empty($user)) {
      Responder::unauthorized("No user with email $email");
    }

    $res_valid = Authorizer::validate($user->id, $password);
    if ($res_valid->isErr()) {
      return Responder::server_error($res_valid->unwrapErr());
    }

    if (!$res_valid->unwrap()) {
      return Responder::unauthorized("Incorrect password");
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
    return Responder::success();
  }

  // GET /user
  public static function get_user()
  {
    $auth_token = Authorizer::validate_token_header();

    if (!$auth_token->is_valid()) {
      return Responder::unauthorized($auth_token->message());
    }

    $user = User::get_by_id($auth_token->user_id());

    if (empty($user)) {
      Responder::not_found("user not found");
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
      return Responder::unauthorized($auth_token->message());
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
      return Responder::unauthorized($auth_token->message());
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
      return Responder::unauthorized($auth_token->message());
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

  //POST user/cart
  public static function add_to_cart()
  {
    $data = get_input_json();
    if (!has_required_keys($data, ['listing_id', 'count'])) {
      Responder::bad_request("Invalid json params");
      return;
    }

    $auth_token = Authorizer::validate_token_header();

    if (!$auth_token->is_valid()) {
      return Responder::unauthorized($auth_token->message());
    }

    $user = User::get_by_id($auth_token->user_id());
    $listing = Listing::get_by_id(trim($data["listing_id"]));

    if (empty($user)) {
      return Responder::not_found("No user found matching auth token");
    }
    if (empty($listing)) {

      return Responder::not_found("No listing found matching id");
    }

    $result = Cart::add_to_cart($user, $listing, $data["count"]);

    if ($result->isErr()) {
      return Responder::error($result->unwrapErr());
    }

    return Responder::success();
  }

  //GET user/cart
  public static function get_cart()
  {

    $auth_token = Authorizer::validate_token_header();

    if (!$auth_token->is_valid()) {
      return Responder::unauthorized($auth_token->message());
    }

    $user = User::get_by_id($auth_token->user_id());

    if (empty($user)) {
      return Responder::not_found("No user found matching auth token");
    }


    $result = Cart::get_cart($user);

    if ($result->isErr()) {
      return Responder::error($result->unwrapErr());
    }

    return Responder::success($result->unwrap());
  }
  //POST user/cart/checkout
  public static function checkout()
  {
    $data = get_input_json();
    if (!has_required_keys($data, ['payment_meta'])) {
      Responder::bad_request("Invalid json params");
      return;
    }
    $auth_token = Authorizer::validate_token_header();

    if (!$auth_token->is_valid()) {
      return Responder::unauthorized($auth_token->message());
    }

    $user = User::get_by_id($auth_token->user_id());

    if (empty($user)) {
      return Responder::not_found("No user found matching auth token");
    }


    $result_cart = Cart::checkout($user);

    if ($result_cart->isErr()) {
      return Responder::error($result_cart->unwrapErr());
    }

    $order_result = Order::create_order($result_cart->unwrap());

    if ($order_result->isErr()) {
      return Responder::server_error($order_result->unwrapErr());
    }

    return Responder::success();
  }

  //GET users/orders
  public static function get_orders()
  {

    $auth_token = Authorizer::validate_token_header();

    if (!$auth_token->is_valid()) {
      return Responder::unauthorized($auth_token->message());
    }

    $user = User::get_by_id($auth_token->user_id());

    if (empty($user)) {
      return Responder::not_found("No user found matching auth token");
    }


    $result = Order::get_orders($user);

    if ($result->isErr()) {
      return Responder::error($result->unwrapErr());
    }

    if (empty($result->unwrap())) {
      return Responder::not_found("No orders matching user");
    }

    return Responder::success($result->unwrap());
  }
}
