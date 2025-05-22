<?php

require_once './backend/db/Database.php';
require_once './backend/core/Result.php';
require_once './backend/core/Authorizer.php';
class UserEditSubmission
{

  public string $contact;
  public int $id;
  public function __construct($id, $data)
  {
    $this->id = $id;
    $this->contact = $data["contact"] ?? "";
  }
}

class User
{
  public int $id;
  public string $name;
  public string $email;
  public string $contact;

  public function __construct(array $data)
  {
    $this->id = $data['user_id'];
    $this->name = $data['name'];
    $this->email = $data['email'];
    $this->contact = $data['contact'];
  }

  public static function get_by_id(string $user_id): ?User
  {
    try {
      Database::connect();

      $db = Database::db();
      $stmt = $db->prepare("SELECT * FROM users WHERE user_id = :userid LIMIT 1");
      $stmt->bindValue(':userid', $user_id);
      $stmt->execute();

      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row) {
        $user =  new User($row);
        return  $user;
      }
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }

    return null;
  }

  public static function get_by_email(string $email = ""): ?User
  {
    try {
      Database::connect();

      $db = Database::db();
      $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
      $stmt->bindValue(':email', $email);
      $stmt->execute();

      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row) {
        $user =  new User($row);
        return  $user;
      }
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }

    return null;
  }


  public static function  create($data, $password): Result
  {
    try {
      Database::connect();

      $db = Database::db();
      $db->beginTransaction();
      $stmt = $db->prepare("INSERT INTO users (name, email, contact) VALUES (:name, :email, :contact)");
      $stmt->execute([
        ':name' => trim($data['name']),
        ':email' => trim($data['email']),
        ':contact' => trim($data['contact']),
      ]);
      $last_id = $db->lastInsertId();
      Authorizer::store_validation($db, $last_id, $password);
      $db->commit();
      return Result::Ok(0);
    } catch (PDOException $e) {
      return Result::Err("Error: " . $e->getMessage());
    }
  }

  public static function delete(int $user_id): Result
  {
    try {
      $db = Database::db();
      $stmt = $db->prepare("DELETE * FROM users WHERE user_id = :user_id");
      $stmt->execute([':user_id' => $user_id]);
    } catch (PDOException $e) {
      return Result::Err("Error: " . $e->getMessage());
    }
    return Result::Ok(null);
  }

  public static function update_user_info(UserEditSubmission $edit): Result
  {

    try {
      Database::connect();
      $db = Database::db();
      $db->beginTransaction();
      if (!empty($edit->contact)) {
        self::update_contact($db, $edit->id, $edit->contact);
      }
      $db->commit();
    } catch (PDOException $e) {
      return Result::Err("Error: " . $e->getMessage());
    }
    return Result::Ok(null);
  }
  public static function update_password(int $user_id, string $password, string $old_password): Result
  {
    try {
      Database::connect();
      $db = Database::db();
      $db->beginTransaction();
      $updated = Authorizer::update_validation($db, $user_id, $password, $old_password);
      $db->commit();
      return Result::Ok($updated);
    } catch (PDOException $e) {
      return Result::Err("Error: " . $e->getMessage());
    }
  }

  public static function update_contact($db, int $user_id, string $contact)
  {
    $stmt = $db->prepare("UPDATE users SET contact = :contact WHERE user_id = :id");
    $stmt->execute([
      ':contact' => $contact,
      ':id' => $user_id,
    ]);
  }
}
