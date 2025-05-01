<?php 

require_once'./backend/db/Database.php';

class User {
    public string $id;
    public string $name;
    public string $email;
    public string $contact;

    public function __construct(string $id, string $name, string $email, string $contact) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->contact = $contact;
    }

    public static function get(string $email): ?User {
        try {
            Database::connect(); 
    
            $db = Database::db();
            $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindValue(':email', $email);
            $stmt->execute();
    
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return new User(
                    $row['user_id'],
                    $row['name'],
                    $row['email'],
                    $row['contact']
                );
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    
        return null;
    }
}

?>