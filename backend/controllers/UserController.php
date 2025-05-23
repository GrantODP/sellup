<?php
require_once './backend/domain/User.php';
require_once './backend/domain/Listing.php';
require_once './backend/domain/Cart.php';
require_once './backend/domain/Order.php';
require_once './backend/domain/Review.php';
require_once './backend/domain/Message.php';
require_once './backend/domain/Payment.php';
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

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return Responder::bad_request($email . " is not a valid email");
    }

    if (Authorizer::is_valid($password)) {
      return Responder::bad_request("Password does not meet criteria");
    }

    if (User::get_by_email($email)) {
      Responder::error(message: 'User already exists', status: 409);
      return;
    }

    $result = User::create($data, $password);

    if ($result->isErr()) {
      return Responder::error('Error:' . $result->unwrapErr(), 500);
    }

    return Responder::success();
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
  // PUT /user
  public static function update()
  {
    $data = get_input_json();
    $auth_token = Authorizer::validate_token_header();

    if (!$auth_token->is_valid()) {
      return Responder::unauthorized($auth_token->message());
    }

    $user = User::get_by_id($auth_token->user_id());

    if (empty($user)) {
      return Responder::not_found("user not found matching auth");
    }
    $sub = new UserEditSubmission($user->id, $data);
    $result = User::update_user_info($sub);

    if ($result->isErr()) {
      return Responder::server_error("Unable to update user info: " . $result->unwrapErr());
    }
  }

  // PUT /user/password
  public static function update_password()
  {

    $auth_token = Authorizer::validate_token_header();

    if (!$auth_token->is_valid()) {
      return Responder::unauthorized($auth_token->message());
    }

    $data = get_input_json();
    if (!has_required_keys($data, ['old_password', 'password'])) {
      Responder::bad_request("No password or old password provided");
      return;
    }

    $old = trim($data['old_password']);
    $password = trim($data['password']);

    $user = User::get_by_id($auth_token->user_id());

    if (empty($user)) {
      return Responder::not_found("user not found");
    }

    $result = User::update_password($user->id, $password, $old);

    if ($result->isErr()) {
      return Responder::server_error($result->unwrapErr());
    }

    if ($result->unwrap()) {
      return Responder::success();
    }
    return Responder::unauthorized('Old password does not match original password');
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
  //DELETE user/cart
  public static function delete_cart_item()
  {
    $listing_id = $_GET["id"] ?? 0;
    if (empty($listing_id)) {
      return Responder::bad_request('No listing id provided');
    }

    $auth_token = Authorizer::validate_token_header();

    if (!$auth_token->is_valid()) {
      return Responder::unauthorized($auth_token->message());
    }

    $user = User::get_by_id($auth_token->user_id());

    if (empty($user)) {
      return Responder::not_found("No user found matching auth token");
    }


    $result = Cart::remove_from_cart($user->id, $listing_id);

    if ($result->isErr()) {
      return Responder::error($result->unwrapErr());
    }

    return Responder::success();
  }

  //POST user/cart/checkout
  public static function checkout()
  {

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

    //Single order
    $order_id = $_GET["id"] ?? 0;
    if ($order_id) {
      $order = Order::get_order($order_id);
      if (empty($order)) {
        return Responder::not_found("Order for user not found");
      }
      if ($order->user_id != $user->id) {
        return Responder::forbidden("User not authorized to view order");
      }
      return Responder::success($order);
    } else {
      //get all
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
  // DELETE user/orders
  public static function delete_order()
  {

    $auth_token = Authorizer::validate_token_header();

    if (!$auth_token->is_valid()) {
      return Responder::unauthorized($auth_token->message());
    }

    $user = User::get_by_id($auth_token->user_id());

    if (empty($user)) {
      return Responder::not_found("No user found matching auth token");
    }

    $order_id = $_GET["id"] ?? 0;
    if (empty($order_id)) {
      return Responder::not_found("Order for user not found");
    }
    $result = Order::delete_order($user, $order_id);
    if ($result->isErr()) {
      return Responder::server_error($result->unwrapErr());
    }

    $changed = $result->unwrap();

    if (!$changed) {
      return Responder::not_found("User does not have an order matching id");
    }

    return Responder::success($result->unwrap());
  }

  //POST users/orders/pay
  public static function pay_order()
  {

    $auth_token = Authorizer::validate_token_header();

    if (!$auth_token->is_valid()) {
      return Responder::unauthorized($auth_token->message());
    }

    $data = get_input_json();

    if (!has_required_keys($data, ['order_id', 'payment_meta'])) {
      Responder::bad_request("Invalid json params");
      return;
    }
    $user = User::get_by_id($auth_token->user_id());

    if (empty($user)) {
      return Responder::not_found("No user found matching auth token");
    }


    $order = Order::get_order(trim($data['order_id']));

    if (empty($order)) {
      return Responder::not_found("No order matching id");
    }

    if ($order->user_id != $user->id) {
      return Responder::forbidden("User not authorized to pay order");
    }

    $payemnt = Payment::pay_order($order);

    if ($payemnt->isErr()) {
      return Responder::server_error($payemnt->unwrapErr());
    }


    if (!$payemnt->unwrap()) {
      return Responder::bad_request("Payment for $order->order_id failed");
    }

    return Responder::success($order);
  }

  //PUT users/review
  public static function edit_review()
  {

    $auth_token = Authorizer::validate_token_header();

    if (!$auth_token->is_valid()) {
      return Responder::unauthorized($auth_token->message());
    }

    $data = get_input_json();
    if (!has_required_keys($data, ['message', 'rating'])) {
      Responder::bad_request("Invalid json params");
      return;
    }
    $user = User::get_by_id($auth_token->user_id());

    if (empty($user)) {
      return Responder::not_found("No user found matching auth token");
    }

    $review = Review::get_review(trim($data["review_id"]));

    if (!$review) {
      return Responder::not_found("Review matching id not found");
    }
    $uid = $review["user_id"] ?? 0;

    if ($uid != $user->id) {
      return Responder::forbidden("User not owner of review");
    }

    $result = Review::edit_review($review['review_id'], $data['message'], $data['rating']);
    if ($result->isErr()) {
      return Responder::error($result->unwrapErr());
    }

    return Responder::success();
  }
}
