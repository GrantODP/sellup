<?php

require_once './backend/db/Database.php';
require_once './backend/core/Result.php';
require_once './backend/domain/Cart.php';
require_once './backend/domain/Listing.php';


class Order
{

  public int $order_id;
  public int $items;

  public function __construct(int $order_id, $items)
  {
    $this->order_id = $order_id;
    $this->items = $items;
  }

  public static function create_order(Cart $cart): Result
  {
    if (!$cart->has_items()) {
      return Result::Err("No items to construct a order");
    }
    $ids = array_keys($cart->cart_items);
    $listings = Listing::get_listings($ids);
    $total_amount = self::calc_total($cart, $listings);
    try {
      Database::connect();
      $db = Database::db();

      $db->beginTransaction();


      $stmt_order = $db->prepare("
        INSERT INTO orders (user_id, total_amount) 
        VALUES (:user_id, :total_amount)
      ");

      $stmt_order->execute([
        ':user_id' => $cart->user_id,
        ':total_amount' => $total_amount,
      ]);

      $order_id = $db->lastInsertId();

      $stmt_item = $db->prepare("
        INSERT INTO order_items (order_id, listing_id, quantity, price) 
        VALUES (:order_id, :listing_id, :quantity, :price)
    ");

      foreach ($listings as $item) {
        $id = $item['listing_id'];
        $stmt_item->execute([
          ':order_id' => $order_id,
          ':listing_id' => $id,
          ':quantity' => $cart->cart_items[$id],
          ':price' => $item['price'],
        ]);
      }

      $db->commit();
      return Result::Ok(null);
    } catch (PDOException $e) {
      return Result::Err($e->getMessage());
    }
    return Result::Err("Unexpected error");
  }

  public static function get_orders(User $user): Result
  {
    try {
      Database::connect();
      $db = Database::db();

      $stmt = $db->prepare("SELECT * FROM orders WHERE user_id = :id");

      $stmt->execute([
        ':id' => $user->id,
      ]);

      $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if (empty($orders)) {
        return Result::Ok(null);
      }
      return Result::Ok($orders);
    } catch (PDOException $e) {
      return Result::Err($e->getMessage());
    }
    return Result::Err("Unexpected error");
  }

  public static function get_order(int $order_id): ?Order
  {
    try {
      Database::connect();
      $db = Database::db();

      $stmt = $db->prepare("SELECT listing_id, quantity, price, subtotal FROM order_items WHERE order_id = :id");

      $stmt->execute([
        ':id' => $order_id,
      ]);

      $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if (empty($order_items)) {
        return null;
      }
      return new Order($order_id, $order_items);
    } catch (PDOException $e) {
      echo $e->getMessage();
      return null;
    }
  }

  public static function calc_total(Cart $cart, array $listings): float
  {
    $amount = 0.00;

    foreach ($listings as $listing) {
      $id = $listing['listing_id'];
      $price = $listing['price'];
      $count = $cart->cart_items[$id];
      $amount = $amount + ($price * $count);
    }

    return $amount;
  }
}
