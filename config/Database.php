<?php
/**
 * Conexión Database PDO - Configuración para servidor de producción
 * Datos de conexión según connect.php original
 */
class Database {
    private static $instance = null;
    private $connection = null;

    /*
    // Configuración para servidor de producción
    private $host = 'localhost';
    private $dbname = 'systecso_app'; // Ajustar según tu base de datos del servidor
    private $username = 'systecso_userapp'; // Ajustar según tu usuario del servidor
    private $password = ',b}n-Hz@#!xeiP7Q'; // Ajustar según tu contraseña del servidor
    private $port = 3306; // Puerto estándar de MySQL
    */
    // Configuración alternativa para MAMP local (comentada)
    private $host = 'localhost';
    private $dbname = 'systec_new';
    private $username = 'root';
    private $password = 'root';
    private $port = 3306;

    private function __construct() {
        try {
            // Configuración para MAMP local con socket Unix
            $dsn = "mysql:unix_socket=/Applications/MAMP/tmp/mysql/mysql.sock;dbname={$this->dbname};charset=utf8mb4";

            // Configuración alternativa para servidor de producción (comentada)
            //$dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";

            $this->connection = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}