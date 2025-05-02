<?php
require_once './backend/core/Result.php';
require_once './backend/db/Database.php';

class Listing
{

  public string $listing_id;
  public string $seller_id;
  public string $price;
  public string $date;
  public string $status;


  public function __construct(string $id, string $seller_id, string $price, string $date, string $status)
  {
    $this->listing_id = $id;
    $this->seller_id = $seller_id;
    $this->price = $price;
    $this->date = $date;
    $this->status = $status;
  }


  public function post(): Result
  {

    try {
      Database::connect();

      $db = Database::db();
      $stmt = $db->prepare("INSERT INTO listings (listing_id,seller_id, price, date, status) VALUES (:id, :sellid, :price, :date, :status)");
      $stmt->execute([
        ':id' => $this->listing_id,
        ':sellid' => $this->seller_id,
        ':price' => $this->price,
        ':date' => $this->date,
        ':status' => $this->status,
      ]);
    } catch (PDOException $e) {
      return Result::Err("Error: " . $e->getMessage());
    }
    return Result::Ok(null);
  }
}
