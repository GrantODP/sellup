<?php

require_once './backend/core/Token.php';



class Authorizer
{

  public static $tokens = [];
  public static $token_duration = 900;






  public static function validate_token_header(): Token
  {
    $headers = getallheaders();
    $auth_header = $headers['Authorization'];
    if ($auth_header == null) {
      return new Token([], TokenStatus::Missing, '');
    }

    $token = str_replace('Bearer ', '', $auth_header);
    $token_result = Tokener::get_user_id_from_token($token);

    if (!$token_result->isOk()) {
      return new Token([], TokenStatus::Invalid, '');
    }

    return new Token($token_result->unwrap(), TokenStatus::Valid, $token_result->unwrap());
  }
}
