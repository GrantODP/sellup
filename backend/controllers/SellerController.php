<?php
require_once './backend/domain/User.php';
require_once './backend/domain/Listing.php';
require_once './backend/domain/Location.php';
require_once './backend/domain/Seller.php';
require_once './backend/domain/Rating.php';
require_once './backend/core/Token.php';;
require_once './backend/core/Authorizer.php';;
require_once './backend/util/Util.php';


class SellerController
{


  // POST /listings
  public static function post_listing()
  {
    $input = get_input_json();
    if (empty($input)) {
      return Responder::bad_request("No json provided or failed parsing json");
    }
    if (!has_required_keys($input, ['price', 'cat_id', 'province', 'city', 'title'])) {
      return Responder::bad_request("Missing one or more of the following parameters: {price, cat_id, province, city, title");
    }

    $auth_token = Authorizer::validate_token_header();

    if (!$auth_token->is_valid()) {
      return Responder::unauthorized($auth_token->message());
    }

    $seller = Seller::get_or_insert($auth_token->user_id());

    $location = Location::get_or_insert(
      sentence_case($input['province']),
      sentence_case($input['city'])
    );
    if (empty($seller)) {
      return Responder::server_error("Unable to create a seller");
    }

    if ($location->isErr()) {
      $error = $location->unwrapErr();
      return Responder::server_error("Unable to find or make location: $error");
    }

    $input['seller_id'] = $seller->seller_id;
    $input['location_id'] = $location->unwrap();
    $listing_submission = new ListingSubmission($input);
    $list_result = Listing::post($listing_submission);

    if ($list_result->isErr()) {
      return Responder::server_error("Error posting a ad:" . $list_result->unwrapErr());
    }

    return Responder::success();
  }

  // GET /seller
  public static function get_seller()
  {

    $id = $_GET['id'] ?? null;
    if ($id === null) {
      return Responder::bad_request("missing id");
    }

    $seller = Seller::get_seller($id);

    if ($seller == null) {
      return Responder::server_error("Unable to find seller");
    }


    return Responder::success($seller);
  }
  // GET /seller/rating
  public static function get_rating()
  {

    $id = $_GET['id'] ?? null;
    if ($id === null) {
      return Responder::bad_request("missing id");
    }

    $rating = Rating::get_seller_score($id);

    if ($rating == null) {
      return Responder::server_error("Unable to find rating for seller: " . $id);
    }

    return Responder::success($rating);
  }
}
