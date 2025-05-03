<?php

require_once './backend/db/Database.php';
require_once './backend/core/Result.php';

class User
{
  public string $id;
  public string $name;
  public string $email;
  public string $contact;
  public string $password;

  public function __construct(array $data)
  {
    $this->id = $data['user_id'];
    $this->name = $data['name'];
    $this->email = $data['email'];
    $this->contact = $data['contact'];
    $this->password = $data['password'];
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


  public static function  create($data): Result
  {

    try {
      Database::connect();

      $db = Database::db();
      $stmt = $db->prepare("INSERT INTO users (name, email, contact, password) VALUES (:name, :email, :contact, :password)");
      $stmt->execute([
        ':name' => $data['name'],
        ':email' => $data['email'],
        ':contact' => $data['contact'],
        ':password' => $data['password'],
      ]);
    } catch (PDOException $e) {
      return Result::Err("Error: " . $e->getMessage());
    }
    return Result::Ok(null);
  }

  public static function delete(int $user_id): Result
  {
    try {
      $db = Database::db();
      $stmt = $db->prepare("DELETE 1 FROM users WHERE user_id = :user_id");
      $stmt->execute([':user_id' => $user_id]);
    } catch (PDOException $e) {
      return Result::Err("Error: " . $e->getMessage());
    }
    return Result::Ok(null);
  }

  public static function update_name(int $user_id, string $name): Result
  {
    try {
      $db = Database::db();
      $stmt = $db->prepare("UPDATE users SET name = :name WHERE user_id = :user_id");
      $stmt->execute([
        ':user_id' => $user_id,
        ':name' => $name
      ]);
    } catch (PDOException $e) {
      return Result::Err("Error: " . $e->getMessage());
    }
    return Result::Ok(null);
  }

  public static function update_contact(int $user_id, string $name): Result
  {
    try {
      $db = Database::db();
      $stmt = $db->prepare("UPDATE users SET contact = :contact WHERE user_id = :user_id");
      $stmt->execute([
        ':contact' => $user_id,
        ':name' => $name
      ]);
    } catch (PDOException $e) {
      return Result::Err("Error: " . $e->getMessage());
    }
    return Result::Ok(null);
  }
}
