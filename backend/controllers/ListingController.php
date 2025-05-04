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
}
