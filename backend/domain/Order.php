<?php

require_once './backend/db/Database.php';
require_once './backend/core/Result.php';
require_once './backend/domain/Cart.php';
require_once './backend/domain/Listing.php';


class Order
{

  public int $order_id;
  public array $items;
  public int $user_id;
  public float $total;

  public function __construct(array $items, array $order)
  {
    $this->order_id = $order['order_id'];
    $this->items = $items;
    $this->total = $order['total_amount'];
    $this->user_id = $order['user_id'];
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
      $stmt_total = $db->prepare("SELECT total_amount, user_id, order_id FROM orders WHERE order_id = :id");

      $stmt->execute([
        ':id' => $order_id,
      ]);
      $stmt_total->execute([
        ':id' => $order_id,
      ]);


      $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $total = $stmt_total->fetch(PDO::FETCH_ASSOC);

      if (empty($order_items)) {
        return null;
      }

      if (empty($stmt_total)) {
        return null;
      }
      return new Order($order_items, $total);
    } catch (PDOException $e) {
      echo $e->getMessage();
      return null;
    }
  }
  public static function delete_order(User $user, int $order_id): Result
  {

    try {
      Database::connect();
      $db = Database::db();

      $stmt = $db->prepare("DELETE FROM orders WHERE order_id = :order_id AND user_id = :user_id");
      $stmt->execute([
        ':order_id' => $order_id,
        ':user_id' => $user->id,
      ]);

      if ($stmt->rowCount() === 0) {
        return Result::Ok(false);
      }
      return Result::Ok(true);
    } catch (PDOException $e) {
      return Result::Err($e->getMessage());
    }
  }

  public function pay($db)
  {
    $stmt = $db->prepare("UPDATE orders SET status = 'paid' WHERE order_id = :id");

    $stmt->execute([
      ':id' => $this->order_id,
    ]);
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
