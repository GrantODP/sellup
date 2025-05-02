<?php
require_once './backend/core/Result.php';
require_once './backend/db/Database.php';

class Listing
{

  public string $listing_id;
  public string $seller_id;
  public string $price;
  public string $date_posted;


  public function __construct(string $seller_id, string $price)
  {
    $this->listing_id = "0";
    $this->seller_id = $seller_id;
    $this->price = $price;
    $this->date_posted = date('Y-m-d H:i:s');
  }


  public function post(): Result
  {

    try {
      Database::connect();

      $db = Database::db();
      $stmt = $db->prepare("INSERT INTO listings (seller_id, price) VALUES (:sellid, :price)");
      $stmt->execute([
        ':sellid' => $this->seller_id,
        ':price' => $this->price,
      ]);
    } catch (PDOException $e) {
      return Result::Err("Error: " . $e->getMessage());
    }
    return Result::Ok(null);
  }

  /*public static function get(int $listing_id): Result {*/
  /**/
  /*}*/
}
