<?php
require_once './backend/domain/User.php';
require_once './backend/domain/Listing.php';
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
}
