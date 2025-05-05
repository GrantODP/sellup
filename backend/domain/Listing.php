<?php
require_once './backend/core/Result.php';
require_once './backend/db/Database.php';
require_once './backend/util/Util.php';
class ListingSubmission
{
  public string $seller_id;
  public string $price;
  public string $cat_id;
  public string $location_id;
  public string $title;
  public ?string $description;
  public string $slug;

  public function __construct(array $data)
  {
    $this->seller_id = $data['seller_id'];
    $this->price = $data['price'];
    $this->cat_id = $data['cat_id'];
    $this->location_id = $data['location_id'];
    $this->title = $data['title'];
    $this->description = $data['description'] ?? null;
    $this->slug = $this->seller_id  . '-' .  gen_slug($this->title);
  }
}

class Listing
{

  public string $listing_id;
  public string $seller_id;
  public string $price;
  public string $date_posted;
  public string $cat_id;
  public string $location_id;
  public string $title;
  public ?string $description;
  public string $slug;

  public function __construct(array $data)
  {
    $this->listing_id = $data['listing_id'];
    $this->seller_id = $data['seller_id'];
    $this->price = $data['price'];
    $this->date_posted = $data['date_posted'];
    $this->cat_id = $data['cat_id'];
    $this->location_id = $data['location_id'];
    $this->title = $data['title'];
    $this->description = $data['description'] ?? null;
    $this->slug = $data['slug'] ?? null;
  }


  public static function post(ListingSubmission $submission): Result
  {

    try {
      Database::connect();

      $db = Database::db();
      $db->beginTransaction();
      $stmt_list = $db->prepare("INSERT INTO listings (seller_id, price) VALUES (:sellid, :price)");
      $stmt_list->execute([
        ':sellid' => $submission->seller_id,
        ':price' => $submission->price,
      ]);

      $listing_id = $db->lastInsertId();
      $stmt_ad = $db->prepare("INSERT INTO listing_ad (listing_id, cat_id, location_id, title, description, slug) VALUES (:listid, :cat_id, :loc, :title, :descp, :slug)");
      $stmt_ad->execute([
        ':listid' => $listing_id,
        ':cat_id' => $submission->cat_id,
        ':loc' => $submission->location_id,
        ':title' => $submission->title,
        ':descp' => $submission->description,
        ':slug' => $submission->slug,
      ]);
      $db->commit();
    } catch (PDOException $e) {
      return Result::Err("Error: " . $e->getMessage());
    }
    return Result::Ok(null);
  }

  public static function get_by_id(int $listing_id): ?Listing
  { {
      try {
        Database::connect();

        $db = Database::db();
        $stmt = $db->prepare('SELECT * FROM listing_details WHERE listing_id = :id');
        $stmt->bindValue(':id', $listing_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
          $listing =  new Listing($row);
          return  $listing;
        }
      } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
      }

      return null;
    }
  }

  public static function get_by_slug(string $slug): ?Listing
  {
    try {
      Database::connect();

      $db = Database::db();


      $stmt = $db->prepare('SELECT * FROM listing_details WHERE slug = :slug');
      $stmt->bindValue(':slug', $slug);
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if (empty($row)) {
        return null;
      }
      $listing =  new Listing($row);
      return  $listing;
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }

    return null;
  }
}
