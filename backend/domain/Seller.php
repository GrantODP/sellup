<?php
require_once './backend/core/Result.php';
require_once './backend/db/Database.php';

class Seller
{

  public string $seller_id;
  public string $verification;
  public string $created_at;
  public string $name;
  public string $email;
  public string $contact;



  public function __construct(array $seller)
  {
    $this->seller_id = $seller['seller_id'];
    $this->created_at = $seller['created_at'];
    $this->verification = $seller['verification_status'];
    $this->name = $seller['user_name'];
    $this->email = $seller['email'];
    $this->contact = $seller['contact'];
  }


  public static function post(int $user_id): Result
  {

    try {
      Database::connect();

      $db = Database::db();
      $stmt = $db->prepare("INSERT INTO sellers (user_id) VALUES (:userid)");
      $stmt->execute([
        ':userid' => $user_id,
      ]);
    } catch (PDOException $e) {
      return Result::Err("Error: " . $e->getMessage());
    }
    return Result::Ok(null);
  }


  public static function get_seller(int $seller_id): ?Seller
  {

    try {
      Database::connect();

      $db = Database::db();
      $stmt = $db->prepare("SELECT * FROM seller_user_details WHERE seller_id = :seller LIMIT 1");
      $stmt->bindValue(':seller', $seller_id);
      $stmt->execute();

      $row = $stmt->fetch(PDO::FETCH_ASSOC);


      if ($row) {
        $seller =  new Seller($row);
        return  $seller;
      }
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
    return null;
  }

  public static function get_user_id(int $seller_id): ?int
  {

    try {
      Database::connect();

      $db = Database::db();
      $stmt = $db->prepare("SELECT user_id FROM sellers WHERE seller_id = :seller LIMIT 1");
      $stmt->bindValue(':seller', $seller_id);
      $stmt->execute();

      $id = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($id) {
        return  $id['user_id'];
      }
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
    return null;
  }

  public static function get_seller_by_user_id(int $user_id): ?Seller
  {

    try {
      Database::connect();

      $db = Database::db();
      $stmt = $db->prepare("SELECT * FROM seller_user_details WHERE user_id = :user_id LIMIT 1");
      $stmt->bindValue(':user_id', $user_id);
      $stmt->execute();

      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row) {
        $seller =  new Seller($row);
        return  $seller;
      }
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
    return null;
  }

  public static function get_or_insert(int $user_id): ?Seller
  {

    $seller = self::get_seller_by_user_id($user_id);

    if ($seller != null) {
      return $seller;
    }

    $result = self::post($user_id);

    if (!$result->isOk()) {
      return null;
    }

    return self::get_seller_by_user_id($user_id);
  }
}
