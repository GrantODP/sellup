<?php

require_once './backend/core/Token.php';
require_once './backend/core/Result.php';
require_once './backend/util/Util.php';
require_once './backend/db/Database.php';



class Authorizer
{

  public static $tokens = [];
  public static $token_duration = 900;


  public static function validate_token_header(): Token
  {
    $headers = getallheaders();
    $auth_header = $headers['authorization'] ?? null;

    if (empty($auth_header)) {
      return new Token([], TokenStatus::Missing, 'No authorization header provided');
    }


    $token = str_replace('Bearer ', '', $auth_header);
    $token_result = Tokener::get_user_id_from_token($token);

    if ($token_result->isErr()) {
      return new Token([], TokenStatus::Invalid, $token_result->unwrapErr());
    }

    return new Token($token_result->unwrap(), TokenStatus::Valid, $token_result->unwrap());
  }


  public static function hash_password($salt, string $password)
  {
    $hash = hash('sha256', $salt . $password);
    return $hash;
  }

  public static function get_salt()
  {
    return bin2hex(random_bytes(16));
  }

  public static function store_validation(int $user_id, string $password): Result
  {
    $salt = self::get_salt();
    $hash = self::hash_password($salt, $password);

    try {
      Database::connect();
      $db = Database::db();

      $sql = "INSERT INTO user_auth (user_id, password_hash, salt) VALUES (:user_id, :password_hash, :salt)";
      $stmt = $db->prepare($sql);

      $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
      $stmt->bindParam(':password_hash', $hash, PDO::PARAM_STR);
      $stmt->bindParam(':salt', $salt, PDO::PARAM_STR);
      return Result::Ok(null);
    } catch (PDOException $e) {
      Result::Err("Error: " . $e->getMessage());
    }

    return Result::Err("Unexpected error");
  }

  public static function validate(int $user_id, $password): Result
  {
    try {
      Database::connect();
      $db = Database::db();

      $sql = "
        SELECT password_hash, salt
        FROM user_auth 
        WHERE user_id = :id
        LIMIT 1
    ";

      $stmt = $db->prepare($sql);
      $stmt->bindParam(':id', $user_id);

      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if (empty($row)) {
        return Result::Err("User for validation not found");
      }
      $salt = $row['salt'];

      $given = self::hash_password($salt, $password);
      $expected = $row['password_hash'];

      if ($given == $expected) {
        return Result::Ok(true);
      }
    } catch (PDOException $e) {
      Result::Err("Error: " . $e->getMessage());
    }

    return Result::Ok(false);
  }
}
