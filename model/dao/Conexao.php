<?php
class Conexao {
    private static $instance;

    private function __construct() {}

    public static function getInstance() {
        if (!isset(self::$instance)) {
            try {
                self::$instance = new PDO(
                    "mysql:host=localhost;port=3308;dbname=locarbd;charset=utf8mb4",
                    "root", // usuário do MySQL
                    "",     // senha do MySQL
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_PERSISTENT => true
                    ]
                );
            } catch (PDOException $e) {
                die("Erro na conexão: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
