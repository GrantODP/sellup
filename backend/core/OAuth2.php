<?php 

enum TokenStatus: string {
    case Expired = 'expired: login in again';
    case Valid = 'valid';
    case Invalid ='invalid: not logged in';
}

class OAuth2 {

    private static $tokens = [];
    public static $token_duration = 900;

    public static function issue(string $user_id): string {
        $token = bin2hex(random_bytes(16));
        
      
        self::$tokens[$token] = [
            'user_id' => $user_id,
            'expire' => self::new_expire_time(),
        ];
        return $token;
    }
    
    public static function contains(string $token): bool {
        return array_key_exists($token,self::$tokens);
    }
    
    
    public static function validate(string $token): TokenStatus {
       if(!isset(self::$tokens[$token])) {
            return TokenStatus::Invalid;
       } 
       
       $token_data = self::$tokens[$token]; 
       if(self::is_expired($token_data['expire'])) {
            
            return TokenStatus::Expired;
       }
       else {
            return TokenStatus::Valid;
       }

       
    }
    
    private static function new_expire_time(): int {
        return time() + self::$token_duration;
    }
    
    private static function is_expired($timestamp): bool {
        return $timestamp < time();
    }
    
    public static function clean_expired_token(): void{
        foreach(self::$tokens as $token =>$data) {
            if(self::is_expired($data['expire'])) {
                unset(self::$tokens['$token']);
            }
        }
    }
    
    

}

?>