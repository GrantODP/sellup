<?php
require_once './backend/core/Result.php';
require_once './backend/db/Database.php';
require_once './backend/util/Util.php';

class Image
{

  public string $path;

  public function __construct($path)
  {
    $this->path = $path;
  }


  public function inline_data(): array
  {
    $image = base64_encode(file_get_contents($this->path));
    $mime = mime_content_type($this->path);
    return [
      'inline_data' => [
        'mime_type' => $mime,
        'data' => $image,
      ],
    ];
  }

  public static function get(int $image_id): ?Image
  {

    try {
      Database::connect();

      $db = Database::db();


      $stmt = $db->prepare('SELECT * FROM images WHERE id = :id');
      $stmt->bindValue(':id', $image_id);
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if (empty($row)) {
        return null;
      }
      $image =  new Image($row['file_path']);
      return  $image;
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }

    return null;
  }

  /** @return Image[]|null */
  public static function get_listing_image_paths(int $listing_id): ?array
  {
    try {
      Database::connect();

      $db = Database::db();


      $stmt = $db->prepare('SELECT file_path FROM images WHERE listing_id = :id');
      $stmt->bindValue(':id', $listing_id);
      $stmt->execute();
      $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
      if (empty($images)) {
        return null;
      }
      return  $images;
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }

    return null;
  }

  /** @return Image[]|null */
  public static function get_listing_images(int $listing_id): ?array
  {
    try {
      Database::connect();

      $db = Database::db();


      $stmt = $db->prepare('SELECT file_path FROM images WHERE listing_id = :id');
      $stmt->bindValue(':id', $listing_id);
      $stmt->execute();
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      if (empty($rows)) {
        return null;
      }
      $images = [];
      foreach ($rows as $row) {
        $images[] = new Image($row['file_path']);
      }
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }

    return null;
  }

  public static function save(int $listing_id): Result
  {
    $uploaded = [];
    $errors = [];
    $target_dir = realpath(__DIR__ . '/../../media/');
    foreach ($_FILES['images']['tmp_name'] as $index => $tmp_name) {
      $name = basename($_FILES['images']['name'][$index]);
      $target = $target_dir . '/' . $listing_id . '-' . $name;

      var_dump($target);
      if (move_uploaded_file($tmp_name, $target)) {
        $uploaded[] = $target;
      } else {
        $errors[] = $name;
      }
    }

    if (empty($uploaded)) {
      return Result::Err($errors);
    }

    $placeholders = [];
    $values = [];
    foreach ($uploaded as $upload) {
      $placeholders[] = '(?, ?)';
      $values[] = $upload;
      $values[] = $listing_id;
    }

    try {
      Database::connect();

      $db = Database::db();
      $sql = "INSERT INTO images (file_path, listing_id) VALUES " . implode(", ", $placeholders);
      $stmt = $db->prepare($sql);
      $stmt->execute($values);
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      return Result::Err($e->getMessage());
    }

    return Result::Ok(null);
  }
}
