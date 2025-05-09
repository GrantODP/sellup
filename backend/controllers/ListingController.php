<?php
require_once './backend/domain/User.php';
require_once './backend/domain/Listing.php';
require_once './backend/domain/Evaluator.php';
require_once './backend/domain/ItemImage.php';
require_once './backend/domain/Review.php';
require_once './backend/core/Token.php';;
require_once './backend/core/Authorizer.php';;
require_once './backend/util/Util.php';


class ListingController
{
  public static function get_listing($listing_slug)
  {
    $slug = $listing_slug ?? null;
    if ($slug === null) {
      return Responder::bad_request("Missing post listing slug");
    }

    $listing = Listing::get_by_slug($slug);

    if ($listing == null) {
      return Responder::not_found("Listing: " . $slug . " not found");
    }

    return Responder::success($listing);
  }
  public static function get_listings_with_cat()
  {
    if (!has_required_keys($_GET, ['id', 'page', 'limit'])) {
      return Responder::bad_request("Missing one or more of the following parameters: ['id', 'page', 'limit']");
    }

    $id = $_GET['id'];
    $sort_val = $_GET['sort'] ?? null;
    $sort_dir = $_GET['dir'] ?? null;
    $page = $_GET['page'];
    $limit = $_GET['limit'];
    $accepted_sort = ['price', 'date', 'title'];
    $accepted_sord_dir = ['asc', 'desc'];
    if (empty($sort_val) || !in_array($sort_val, $accepted_sort)) {
      $sort_val = 'date_posted';
    }

    if (empty($sort_dir)  || !in_array($sort_dir, $accepted_sord_dir)) {
      $sort_dir = 'asc';
    }



    $listings = Listing::get_by_col_and_page('cat_id', $id, $page, $limit, $sort_val, $sort_dir);
    if ($listings === null) {
      return Responder::not_found('Listings not found');
    }
    return Responder::success_paged($page, $limit, $listings);
  }
  // GET /listing/rating
  public static function get_rating()
  {

    $id = $_GET['id'] ?? null;
    if ($id === null) {
      return Responder::bad_request("missing id");
    }

    $rating = Rating::get_listing_score($id);

    if ($rating == null) {
      return Responder::server_error("Unable to find rating for listing: " . $id);
    }

    return Responder::success($rating);
  }

  // GET /listing/reviews
  public static function get_reviews()
  {

    if (!has_required_keys($_GET, ['id'])) {
      return Responder::bad_request("missing id");
    }

    $id = $_GET['id'];

    $reviews = Review::get_listing_reviews($id);

    if ($reviews == null) {
      return Responder::server_error("Unable to find reviews for listing: " . $id);
    }

    return Responder::success($reviews);
  }

  // POST /listing/reviews
  public static function write_review()
  {

    $data = get_input_json();
    if (!has_required_keys($data, ['rating', 'listing_id'])) {
      return Responder::bad_request("Missing 1 or more review parameters ['rating', 'listing_id']");
    }

    $auth_token = Authorizer::validate_token_header();

    if (!$auth_token->is_valid()) {
      return Responder::bad_request($auth_token->message());
    }


    $data['user_id'] = $auth_token->user_id();
    $review = new Review($data);
    $result = $review->write();

    if ($result->isErr()) {
      return Responder::server_error("Unable to write review for listing: " . $review->listing_id);
    }

    return Responder::success();
  }

  //GET /listings/evaluate
  public static function evaluate()
  {

    $id = $_GET['id'] ?? null;
    if ($id === null) {
      return Responder::bad_request("missing id");
    }

    $rating = Rating::get_listing_score($id);

    $listing = Listing::get_by_id($id);

    if (empty($rating) || empty($listing)) {
      return Responder::server_error("Unable to find rating or listing for listing: " . $id);
    }

    $images[] = Image::get_listing_images($listing->listing_id) ?? [];
    $res = AdEvaluator::evaluate($listing, $rating, $images);

    if ($res->isErr()) {
      return Responder::server_error("Unable evaulate listing: " . $res->unwrapErr());
    }

    return Responder::success($res->unwrap());
  }
}
