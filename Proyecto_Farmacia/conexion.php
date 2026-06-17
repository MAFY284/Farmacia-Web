<?php
// Configuración de la conexión a la base de datos
$host = '127.0.0.1'; 
$port = 3308;
$dbname = 'inventory';
$username = 'root';
$password = 'root';

try {
    // Crear conexión PDO
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lanzar excepciones en errores
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Retornar arreglos asociativos
        PDO::ATTR_EMULATE_PREPARES => false, // Evitar emulación de consultas preparadas
    ]);
} catch (PDOException $e) {
    // Si la conexión falla, mostrar mensaje explicativo
    die("⚠️ Error de conexión a la base de datos: " . $e->getMessage() . "<br><br>Por favor, asegúrate de que MySQL está activo en tu servidor local (XAMPP/WAMP) y que has importado el archivo <strong>farmacia.sql</strong> en phpMyAdmin.");
}
?>
