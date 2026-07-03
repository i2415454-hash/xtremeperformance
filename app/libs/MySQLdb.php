<?php
/**
 * Clase de conexión a base de datos con configuración mejorada para Local y Google Cloud
 */
require_once(__DIR__ . '/Config.php');

class MySQLdb
{
    private $host;
    private $usuario;
    private $clave;
    private $db;
    private $puerto;
    private $conn;

   function __construct()
    {
        // Cargar configuración desde .env si existe
        Config::load();
        
        // Buscar variables en el sistema de forma ultra-segura (Cloud Run)
        $instance_connection_name = getenv('INSTANCE_CONNECTION_NAME') ?: ($_SERVER['INSTANCE_CONNECTION_NAME'] ?? ($_ENV['INSTANCE_CONNECTION_NAME'] ?? ''));
        
        $this->db = getenv('DB_NAME') ?: ($_SERVER['DB_NAME'] ?? ($_ENV['DB_NAME'] ?? Config::get('DB_NAME', 'u645180384_taller')));
        $this->usuario = getenv('DB_USER') ?: ($_SERVER['DB_USER'] ?? ($_ENV['DB_USER'] ?? Config::get('DB_USER', 'u645180384_maxi')));
        $this->clave = getenv('DB_PASS') ?: ($_SERVER['DB_PASS'] ?? ($_ENV['DB_PASS'] ?? Config::get('DB_PASS', 'Maxi.123@123')));
        $this->host = getenv('DB_HOST') ?: ($_SERVER['DB_HOST'] ?? ($_ENV['DB_HOST'] ?? Config::get('DB_HOST', 'localhost')));
        $this->puerto = getenv('DB_PORT') ?: ($_SERVER['DB_PORT'] ?? ($_ENV['DB_PORT'] ?? Config::get('DB_PORT', '3306')));
        
        try {
            // Si detecta la instancia de Cloud SQL, se conecta por el Socket Unix
            if (!empty($instance_connection_name)) {
                $dsn = 'mysql:unix_socket=/cloudsql/' . $instance_connection_name . ';dbname=' . $this->db . ';charset=utf8mb4';
            } else {
                // Si no, usa la conexión TCP/IP local estándar
                $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db . ';charset=utf8mb4';
                if (!empty($this->puerto)) {
                    $dsn .= ';port=' . $this->puerto;
                }
            }
            
            $this->conn = new PDO(
                $dsn,
                $this->usuario,
                $this->clave,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_FOUND_ROWS => true,
                    PDO::ATTR_PERSISTENT => false
                ]
            );
        } catch (Exception $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Error de conexión a la base de datos. Contacte al administrador.");
        }
    }

    /**
     * Función MEJORADA: Ahora acepta consultas preparadas y seguras.
     * Devuelve solo la primera fila del resultado.
     */
    public function query(string $sql = '', array $data = []): array
    {
        if (empty($sql)) return [];

        if (empty($data)) {
            // Si no hay datos, es una consulta simple (comportamiento antiguo)
            $stmt = $this->conn->query($sql);
        } else {
            // Si hay datos, usamos una consulta preparada y segura
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($data);
        }
        
        $salida = $stmt->fetch(PDO::FETCH_ASSOC);

        return $salida ? $salida : [];
    }

    /**
     * Función MEJORADA: Ahora acepta consultas preparadas y seguras.
     * Devuelve todas las filas del resultado.
     */
    public function querySelect(string $sql = '', array $data = []): array
    {
        if (empty($sql)) return [];
        
        if (empty($data)) {
            // Si no hay datos, es una consulta simple (comportamiento antiguo)
            $stmt = $this->conn->query($sql);
        } else {
            // Si hay datos, usamos una consulta preparada y segura
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($data);
        }

        $salida = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $salida ? $salida : [];
    }

    //Update, Delete, Insert
    public function queryNoSelect(string $sql, array $data = []): bool
    {
        $salida = false;
        if (empty($data)) {
            if ($this->conn->query($sql)) $salida = true;
        } else {
            if ($this->conn->prepare($sql)->execute($data)) $salida = true;
        }
        return $salida;
    }

    public function queryCrudo($sql = "")
    {
        return $this->conn->query($sql);
    }

    public function getBaseDatos()
    {
        return $this->db;
    }

    public function lastInsertId()
    {
        return $this->conn->lastInsertId();
    }
}
?>
