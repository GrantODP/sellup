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
  public string $date;
  public string $cat_id;
  public string $province;
  public string $city;
  public string $title;
  public ?string $description;
  public string $slug;

  public function __construct(array $data)
  {
    $this->listing_id = $data['listing_id'];
    $this->seller_id = $data['seller_id'];
    $this->price = $data['price'];
    $this->date = $data['date'];
    $this->cat_id = $data['cat_id'];
    $this->province = $data['province'];
    $this->city = $data['city'];
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
  public static function get_all_by_category(string $cat_id): ?array
  {
    try {
      Database::connect();

      $db = Database::db();


      $stmt = $db->prepare('SELECT * FROM listing_details WHERE cat_id = :cat_id');
      $stmt->bindValue(':cat_id', $cat_id);
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if (empty($row)) {
        return null;
      }
      return  $row;
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }

    return null;
  }

  public static function get_all_by_page(int $page, int $count, string $sort = "listing_id", bool $ascend = true): ?array
  {
    try {
      Database::connect();
      $offset = ($page - 1) * $count;
      $order = $ascend ? 'ASC' : 'DESC';
      $db = Database::db();
      $stmt = $db->prepare("SELECT * FROM listing_details ORDER BY :sort_col :order LIMIT :count OFFSET :offset");
      $stmt->bindValue(':sort_col', $sort);
      $stmt->bindValue(':order', $order);
      $stmt->bindValue(':count', $count);
      $stmt->bindValue(':offset', $offset);
      $stmt->execute();

      $lists = $stmt->fetchAll(PDO::FETCH_ASSOC);
      if ($lists) {
        return  $lists;
      }
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
    return null;
  }

  public static function get_by_col_and_page(string $column, int $id, int $page, int $count, string $sort = "date", string $sort_dir = 'asc'): ?array
  {
    try {
      Database::connect();
      $offset = ($page - 1) * $count;
      $order = $sort_dir == 'asc' ? 'ASC' : 'DESC';
      $db = Database::db();
      $stmt = null;
      if ($id == 0) {
        $stmt = $db->prepare("SELECT * FROM listing_details ORDER BY $sort $order LIMIT :count OFFSET :offset");
      } else {
        $stmt = $db->prepare("SELECT * FROM listing_details WHERE $column = :value ORDER BY $sort $order LIMIT :count OFFSET :offset");
        $stmt->bindValue(':value', $id);
      }

      $stmt->bindValue(':count', (int)$count, PDO::PARAM_INT);
      $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
      $stmt->execute();

      $lists = $stmt->fetchAll(PDO::FETCH_ASSOC);
      if ($lists) {
        return  $lists;
      }
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
    return null;
  }

  public static function fuzzy_find(string $search_term, int $cat_id = 0, int $location_id = 0): ?array
  {
    try {
      Database::connect();


      $db = Database::db();
      $sql = "SELECT * FROM listing_details WHERE MATCH (title) AGAINST (:search IN NATURAL LANGUAGE MODE)";
      $params[':search'] = $search_term;

      if ($cat_id > 0) {
        $sql .= ' AND cat_id= :cid';
        $params['cid'] = $cat_id;
      }
      if ($location_id > 0) {
        $sql .= ' AND location_id= :lid';
        $params['lid'] = $location_id;
      }

      $stmt = $db->prepare($sql);
      $stmt->execute($params);

      $lists = $stmt->fetchAll(PDO::FETCH_ASSOC);
      if ($lists) {
        return  $lists;
      }
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
    return null;
  }

  public static function get_listings(array $ids): ?array
  {
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    try {
      Database::connect();

      $db = Database::db();


      $stmt = $db->prepare("SELECT * FROM listings WHERE listing_id IN ($placeholders)");
      $stmt->execute($ids);
      $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
      var_dump($row);
      if (empty($row)) {
        return null;
      }
      return  $row;
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }

    return null;
  }
}
