<?php
/**
 * Clase para cargar configuración desde archivo .env
 */
class Config
{
    private static $config = [];
    private static $loaded = false;

    public static function load($file = __DIR__ . '/.env')
    {
        if (self::$loaded) return;

        if (!file_exists($file)) {
            // Si no existe .env, usar valores por defecto desde inicio.php
            self::$loaded = true;
            return;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue; // Ignora comentarios
            
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remover comillas si existen
                if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                    (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                    $value = substr($value, 1, -1);
                }
                
                self::$config[$key] = $value;
            }
        }
        
        self::$loaded = true;
    }

    public static function get($key, $default = null)
    {
        self::load();
        
        // 1. PRIORIDAD: Si la variable existe en el sistema (Cloud Run), usa esa primero
        $env_val = getenv($key);
        if ($env_val !== false) {
            return $env_val;
        }
        
        // 2. Si no existe en el sistema, busca en el archivo .env local
        if (isset(self::$config[$key])) {
            return self::$config[$key];
        }
        
        // 3. Si no está en ningún lado, usa el valor por defecto
        return $default;
    }

    public static function has($key)
    {
        self::load();
        return isset(self::$config[$key]);
    }
}
?>
