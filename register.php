<?php
session_start();
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email  = strtolower(trim($_POST['email'] ?? ''));
    $pass   = $_POST['password'] ?? '';
    $plan   = $_POST['plan'] ?? '';

    if (empty($nombre) || empty($email) || empty($pass) || empty($plan)) {
        header("Location: index.php?error=" . urlencode("Todos los campos son obligatorios"));
        exit;
    }

    // Verificar si el correo ya existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        header("Location: index.php?error=" . urlencode("Este correo ya está registrado"));
        exit;
    }

    // Asignar Entrenador aleatorio
    $coaches = [
        ['nombre' => 'Marco "El Toro" Ramírez', 'desc' => 'Especialista en powerlifting y fuerza bruta. 10 años de experiencia.'],
        ['nombre' => 'Sofía Vega', 'desc' => 'Experta en HIIT y transformación corporal. Nutricionista certificada.'],
        ['nombre' => 'Diego "Iron" Mendoza', 'desc' => 'Campeón regional de fisicoculturismo. Enfoque en hipertrofia.'],
        ['nombre' => 'Laura Castillo', 'desc' => 'Especialista en entrenamiento funcional y movilidad.']
    ];
    $coach = $coaches[array_rand($coaches)];

    $precios = ['Basico' => 299, 'Pro' => 499, 'Elite' => 799];
    $monto = $precios[$plan] ?? 299;

    $passHash = password_hash($pass, PASSWORD_BCRYPT);
    $fechaVencimiento = date('Y-m-d', strtotime('+15 days'));

    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, plan, coach, coach_desc, monto, estado_pago, fecha_vencimiento) VALUES (?, ?, ?, ?, ?, ?, ?, 'AL CORRIENTE', ?)");
    $stmt->execute([$nombre, $email, $passHash, $plan, $coach['nombre'], $coach['desc'], $monto, $fechaVencimiento]);

    $_SESSION['user_id'] = $pdo->lastInsertId();
    header("Location: index.php");
    exit;
}
?>
