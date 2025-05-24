<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
  echo "Error [$errno]: $errstr in $errfile on line $errline\n";
  echo "Call Stack:\n";
  debug_print_backtrace();
});
// Load required classes (use autoloading in real projects)
require_once './backend/core/Responder.php';
require_once './backend/core/RequestRouter.php';
require_once './backend/config/Config.php';
require_once './backend/db/Database.php';
require_once './backend/domain/User.php';
require_once './backend/controllers/UserController.php';
require_once './backend/domain/Listing.php';
require_once './backend/domain/Seller.php';
require_once './backend/core/Result.php';


C2Config::load();
Database::connect();




$router = new Router();

//API
//UserController
$router->add_post('/c2c-commerce-site/api/user/create', 'UserController::post');
$router->add_post('/c2c-commerce-site/api/user/report', 'UserController::report');
$router->add_post('/c2c-commerce-site/api/login', 'UserController::login');
$router->add_get('/c2c-commerce-site/api/user', 'UserController::get_user');
$router->add_get('/c2c-commerce-site/api/auth/status', 'UserController::auth_valid');
$router->add_post('/c2c-commerce-site/api/user/message', 'UserController::send_message');
$router->add_post('/c2c-commerce-site/api/user/message-seller', 'UserController::message_seller');
$router->add_get('/c2c-commerce-site/api/user/conversations', 'UserController::get_conversations');
$router->add_get('/c2c-commerce-site/api/user/message', 'UserController::get_messages_by_conversation');
$router->add_post('/c2c-commerce-site/api/user/cart', 'UserController::add_to_cart');
$router->add_get('/c2c-commerce-site/api/user/cart', 'UserController::get_cart');
$router->add_delete('/c2c-commerce-site/api/user/cart', 'UserController::delete_cart_item');
$router->add_post('/c2c-commerce-site/api/user/cart/checkout', 'UserController::checkout');
$router->add_get('/c2c-commerce-site/api/user/orders', 'UserController::get_orders');
$router->add_delete('/c2c-commerce-site/api/user/orders', 'UserController::delete_order');
$router->add_post('/c2c-commerce-site/api/user/orders/pay', 'UserController::pay_order');
$router->add_put('/c2c-commerce-site/api/user', 'UserController::update');
$router->add_put('/c2c-commerce-site/api/user/password', 'UserController::update_password');
$router->add_put('/c2c-commerce-site/api/user/review', 'UserController::edit_review');
//SellerController
$router->add_post('/c2c-commerce-site/api/listings', 'SellerController::post_listing');
$router->add_get('/c2c-commerce-site/api/sellers', 'SellerController::get_seller');
$router->add_get('/c2c-commerce-site/api/sellers/rating', 'SellerController::get_rating');
$router->add_put('/c2c-commerce-site/api/sellers/listings', 'SellerController::update_listing');
$router->add_delete('/c2c-commerce-site/api/sellers/listings', 'SellerController::delete_listing');
//Listing controller
$router->add_get('/c2c-commerce-site/api/listings/{slug}', 'ListingController::get_listing');
$router->add_get('/c2c-commerce-site/api/listings/rating', 'ListingController::get_rating');
$router->add_get('/c2c-commerce-site/api/listings/reviews', 'ListingController::get_reviews');
$router->add_post('/c2c-commerce-site/api/listings/reviews', 'ListingController::write_review');
/*$router->add_get('/c2c- commerce-site/api/listings', 'ListingController::get_listing');*/
$router->add_get('/c2c-commerce-site/api/listings/category', 'ListingController::get_listings_with_cat');
$router->add_get('/c2c-commerce-site/api/listings/evaluate', 'ListingController::evaluate');
$router->add_post('/c2c-commerce-site/api/listings/media', 'ListingController::add_listing_images');
$router->add_get('/c2c-commerce-site/api/listings/media', 'ListingController::get_listing_images');
$router->add_get('/c2c-commerce-site/api/listings/search', 'ListingController::search_listing');
$router->add_get('/c2c-commerce-site/api/listings/preview', 'ListingController::get_listing_preview');

//Categories
$router->add_get('/c2c-commerce-site/api/categories', 'CategoryController::get_all');



//Media
$router->add_get('/c2c-commerce-site/media/{slug}', 'MediaController::get');





//ONLY GETS
//Views 

$router->add_get('/c2c-commerce-site/test', 'TestController::test');
$router->add_get('/c2c-commerce-site/ads/{slug}', 'PageController::get_ad_page');
$router->add_get('/c2c-commerce-site/ads', 'PageController::get_all_ads_page');
$router->add_get('/c2c-commerce-site/users', 'PageController::get_user');
$router->add_get('/c2c-commerce-site/login', 'PageController::login');












$router->handle();
