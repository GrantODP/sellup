<?php

class Config {
    private static $data = [];

    public static function load(string $file): void {
        if (!file_exists($file)) {
            throw new Exception("Config file not found: $file");
        }

        self::$data = parse_ini_file($file, true);
    }

    public static function get(string $table, string $key): mixed {
        return self::$data[$section][$key] ?? null;
    }
}
?>