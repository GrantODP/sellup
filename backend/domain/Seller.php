<?php
require_once './backend/core/Result.php';
require_once './backend/db/Database.php';

class Seller
{

  public string $seller_id;
  public string $user_id;
  public string $verification;
  public string $created_at;


  public function __construct(string $user_id)
  {
    $this->seller_id = "0";
    $this->user_id = $user_id;
    $this->created_at = date('Y-m-d H:i:s');
    $this->verification = "unverified";
  }


  public function post(): Result
  {

    try {
      Database::connect();

      $db = Database::db();
      $stmt = $db->prepare("INSERT INTO sellers (user_id) VALUES (:userid)");
      $stmt->execute([
        ':userid' => $this->user_id,
      ]);
    } catch (PDOException $e) {
      return Result::Err("Error: " . $e->getMessage());
    }
    return Result::Ok(null);
  }
}
