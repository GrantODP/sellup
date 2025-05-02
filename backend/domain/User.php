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

  public function __construct(string $name, string $email, string $contact, string $password)
  {
    $this->name = $name;
    $this->email = $email;
    $this->contact = $contact;
    $this->password = $password;
  }

  public static function get(string $user_id): ?User
  {
    try {
      Database::connect();

      $db = Database::db();
      $stmt = $db->prepare("SELECT * FROM users WHERE user_id = :userid");
      $stmt->bindValue(':userid', $user_id);
      $stmt->execute();

      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row) {
        $user =  new User(
          $row['name'],
          $row['email'],
          $row['contact'],
          $row['password']
        );
        $user->id = $row['user_id'];
        return  $user;
      }
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }

    return null;
  }

  public static function exists(string $email): bool
  {
    $db = Database::db();
    $stmt = $db->prepare("SELECT 1 FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    return (bool) $stmt->fetchColumn();
  }

  public function post(): Result
  {

    try {
      Database::connect();

      $db = Database::db();
      $stmt = $db->prepare("INSERT INTO users (name, email, contact, password) VALUES (:name, :email, :contact, :password)");
      $stmt->execute([
        ':name' => $this->name,
        ':email' => $this->email,
        ':contact' => $this->contact,
        ':password' => $this->password,
      ]);
    } catch (PDOException $e) {
      return Result::Err("Error: " . $e->getMessage());
    }
    return Result::Ok(null);
  }
}
