
<?php 

class Responder {

    public static function json($data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public static function success($data = [], string $message = 'Success', int $status = 200): void {
        self::json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $status);
    }

    public static function error(string $message = 'An error occurred', int $status = 400): void {
        self::json([
            'status' => 'error',
            'message' => $message
        ], $status);
    }

    public static function unauthorized(string $message = 'Unauthorized'): void {
        self::error($message, 401);
    }

    public static function notFound(string $message = 'Not Found'): void {
        self::error($message, 404);
    }

    public static function serverError(string $message = 'Internal Server Error'): void {
        self::error($message, 500);
    }
}


?>