<?php
define("LLAVE1","Hombresneciosque");
define("LLAVE2","acusaisalamujer");
define("CLAVE","mimamamemimamucho");
define('RUTA', '/./');
define("TAMANO_PAGINA",6);
define('PAGINAS_MAXIMAS',4);

// URL absoluta detectada dinámicamente desde Cloud Run o entorno local
// URL absoluta del sitio autodetectada dinámicamente
if (!defined('SITE_URL')) {
    // Intenta leer la variable de entorno primero
    $env_site_url = getenv('SITE_URL') ?: ($_SERVER['SITE_URL'] ?? ($_ENV['SITE_URL'] ?? null));
    
    if ($env_site_url) {
        define('SITE_URL', $env_site_url);
    } else {
        // Si no existe la variable, detecta el protocolo y host actual del navegador
        $protocolo = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || 
                     (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        define('SITE_URL', $protocolo . "://" . $host . "/");
    }
}
// Config correo básico priorizando variables de entorno
if (!defined('MAIL_FROM')) {
    $env_mail = getenv('MAIL_FROM');
    define('MAIL_FROM', $env_mail ? $env_mail : 'no-reply@xtremeperformancepe.com');
}
if (!defined('MAIL_FROM_NAME')) {
    $env_mail_name = getenv('MAIL_FROM_NAME');
    define('MAIL_FROM_NAME', $env_mail_name ? $env_mail_name : 'Xtreme Performance');
}
if (!defined('MAIL_REPLY_TO')) {
    $env_reply = getenv('MAIL_REPLY_TO');
    define('MAIL_REPLY_TO', $env_reply ? $env_reply : 'contacto@xtremeperformancepe.com');
}
//
//Tipos Usuarios
//
define('ADMON',1);
define('OPERADOR',2);
define('MECANICO',3);
define('CLIENTE',4);
//
//Estados Usuario
//
define('USUARIO_ACTIVO',1);
define('USUARIO_INACTIVO',2);
define('USUARIO_SUSPENDIDO',3);
//
//Tipos Mecánico
//
define('MOTORES',1);
define('TRANSMISIONES',2);
define('FRENOS',3);
define('ELECTRICO',4);
define('HOJALATERIA',5);
//
//Estados mecánico
//
define('MECANICO_DISPONIBLE',1);
define('MECANICO_OCUPADO',2);
define('MECANICO_VACACIONES',3);
//
//Estados cliente
//
define('CLIENTE_ACTIVO',1);
define('CLIENTE_INACTIVO',2);
//
//Estado Orden Reparacion
//
define('ORDEN_ABIERTA',1);
define('ORDEN_FACTURADA',2);
//
date_default_timezone_set('America/Lima');
//
require_once('libs/fpdf.php');
require_once('libs/Imprimir.php');
require_once('libs/ReporteTabla.php');
require_once("libs/Config.php");
require_once("libs/Helper.php");
require_once("libs/Sesion.php");
require_once("libs/Controlador.php");
require_once("libs/Control.php"); 
require_once("libs/MySQLdb.php");
$control = new Control();
?>
