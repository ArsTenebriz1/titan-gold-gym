<?php
$host = 'db_master'; // Nombre del contenedor MariaDB en Docker
$db   = 'titan_gym';
$user = 'root';      // Reemplaza con tu usuario si es diferente
$pass = 'rootpassword';      // Reemplaza con tu contraseña de MariaDB
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    try {
        // Fallback a localhost/127.0.0.1
        $dsn_local = "mysql:host=127.0.0.1;dbname=$db;charset=$charset";
        $pdo = new PDO($dsn_local, $user, $pass, $options);
    } catch (\PDOException $e2) {
        die("Error de conexión a MariaDB: " . $e2->getMessage());
    }
}
?>
