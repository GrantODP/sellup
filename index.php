<?php

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

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
C2Config::load();
Database::connect();




$router = new Router();
//UserController
$router->add_post('/c2c-commerce-site/user/create', 'UserController::get');
$router->add_post('/c2c-commerce-site/login', 'UserController::login');
$router->add_post('/c2c-commerce-site/user/update', 'UserController::update');
$router->add_get('/c2c-commerce-site/user', 'UserController::get_user');
$router->add_post('/c2c-commerce-site/user/message', 'UserController::send_message');
$router->add_post('/c2c-commerce-site/user/message-seller', 'UserController::message_seller');
$router->add_get('/c2c-commerce-site/user/conversations', 'UserController::get_conversations');
$router->add_get('/c2c-commerce-site/user/message', 'UserController::get_messages_by_conversation');

//SellerController
$router->add_post('/c2c-commerce-site/listing', 'SellerController::post_listing');
$router->add_get('/c2c-commerce-site/seller', 'SellerController::get_seller');
$router->add_get('/c2c-commerce-site/seller/rating', 'SellerController::get_rating');
//Listing controller
$router->add_get('/c2c-commerce-site/listing/{slug}', 'ListingController::get_listing');
$router->add_get('/c2c-commerce-site/listing/rating', 'ListingController::get_rating');
$router->add_get('/c2c-commerce-site/listing/reviews', 'ListingController::get_reviews');
$router->add_post('/c2c-commerce-site/listing/reviews', 'ListingController::write_review');
$router->add_post('/c2c-commerce-site/listing/media', 'ListingController::write_review');
/*$router->add_get('/c2c-commerce-site/listings', 'ListingController::get_listing');*/
$router->add_get('/c2c-commerce-site/listings/category', 'ListingController::get_listings_with_cat');
$router->add_get('/c2c-commerce-site/listings/evaluate', 'ListingController::evaluate');
$router->add_post('/c2c-commerce-site/listings/media', 'ListingController::add_listing_images');
$router->add_get('/c2c-commerce-site/listings/search', 'ListingController::search_listing');

//Categories
$router->add_get('/c2c-commerce-site/categories', 'CategoryController::get_all');
$router->handle();



//Media
$router->add_get('/c2c-commerce-site/media/{slug}', 'MediaController::get');
