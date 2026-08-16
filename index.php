<?php
session_start();
require_once 'conexion.php';

$user = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
}

// Configuración dinámica por Plan
$routines = [
    'Basico' => [
        'name' => 'Fuerza Fundamental',
        'desc' => 'Rutina enfocada en los movimientos básicos para construir una base sólida.',
        'steps' => [
            'Calentamiento: 5 min elíptica + movilidad articular',
            'Sentadilla con barra: 4 series x 10 reps',
            'Press de banca: 4 series x 10 reps',
            'Peso muerto rumano: 3 series x 12 reps',
            'Dominadas asistidas: 3 series x 8 reps',
            'Plancha abdominal: 3 series x 45 segundos'
        ]
    ],
    'Pro' => [
        'name' => 'Hipertrofia Avanzada',
        'desc' => 'Volumen moderado-alto con técnicas de intensidad para ganancia muscular.',
        'steps' => [
            'Calentamiento dinámico: 7 min + activación glútea',
            'Sentadilla búlgara: 4 series x 12 reps por pierna',
            'Press de banca inclinado: 4 series x 10 reps',
            'Remo con barra T: 4 series x 12 reps',
            'Elevaciones laterales: 4 series x 15 reps',
            'Curl de bíceps martillo: 3 series x 12 reps'
        ]
    ],
    'Elite' => [
        'name' => 'Protocolo Titan',
        'desc' => 'Entrenamiento de élite con periodización y técnicas avanzadas.',
        'steps' => [
            'Calentamiento específico: 10 min + foam rolling',
            'Sentadilla trasera: 5 series x 5 reps (peso pesado)',
            'Press de banca con pausa: 5 series x 5 reps',
            'Peso muerto convencional: 5 series x 3 reps',
            'Press militar: 4 series x 8 reps',
            'Dominadas con peso: 4 series x 6 reps'
        ]
    ]
];

$diets = [
    'Basico' => [
        ['time' => 'Desayuno', 'desc' => 'Avena con plátano y huevos revueltos', 'cals' => '450 kcal'],
        ['time' => 'Almuerzo', 'desc' => 'Pechuga de pollo, arroz integral, brócoli', 'cals' => '550 kcal'],
        ['time' => 'Snack', 'desc' => 'Yogur griego con almendras', 'cals' => '200 kcal'],
        ['time' => 'Cena', 'desc' => 'Pescado a la plancha con ensalada mixta', 'cals' => '400 kcal']
    ],
    'Pro' => [
        ['time' => 'Desayuno', 'desc' => 'Tortilla de 4 claras + 2 enteros, aguacate, tostada', 'cals' => '520 kcal'],
        ['time' => 'Snack AM', 'desc' => 'Manzana con crema de cacahuate natural', 'cals' => '280 kcal'],
        ['time' => 'Almuerzo', 'desc' => 'Salmón, quinoa, espárragos, aceite de oliva', 'cals' => '620 kcal'],
        ['time' => 'Cena', 'desc' => 'Filete magro, batata al horno, ensalada verde', 'cals' => '480 kcal']
    ],
    'Elite' => [
        ['time' => 'Desayuno', 'desc' => '6 huevos (4 claras), 100g avena, frutos rojos, miel', 'cals' => '650 kcal'],
        ['time' => 'Almuerzo', 'desc' => '300g pechuga de pavo, 200g arroz, verduras', 'cals' => '720 kcal'],
        ['time' => 'Pre-Workout', 'desc' => '200g arroz blanco, 150g pollo, salsa de soja', 'cals' => '550 kcal'],
        ['time' => 'Cena', 'desc' => '250g filete de res magro, 200g pasta integral, ensalada', 'cals' => '680 kcal']
    ]
];

$cals = ['Basico' => '2,200', 'Pro' => '2,800', 'Elite' => '3,500'];
$activePlan = $user['plan'] ?? 'Basico';
$userRoutine = $routines[$activePlan];
$userDiet = $diets[$activePlan];
$userCals = $cals[$activePlan];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TITAN GOLD GYM</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
</head>
<body>

<?php if (!$user): ?>
<div id="auth-section">
  <div class="tg-login-box">
    <h2>⚡ TITAN GOLD GYM</h2>
    <p class="subtitle">Forja tu leyenda. Paga con sangre y sudor.</p>
    
    <?php if (isset($_GET['error'])): ?>
      <div style="color:#c0392b; font-size:0.85rem; margin-bottom:12px;"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <form id="login-form" action="login.php" method="POST">
      <input type="email" name="email" class="tg-input" placeholder="Correo electrónico" required>
      <input type="password" name="password" class="tg-input" placeholder="Contraseña" required>
      <button type="submit" class="tg-btn">Ingresar</button>
      <div class="tg-toggle">
        ¿No tienes cuenta? <a onclick="toggleAuth()">Regístrate aquí</a>
      </div>
    </form>

    <form id="register-form" class="hidden" action="register.php" method="POST">
      <input type="text" name="nombre" class="tg-input" placeholder="Nombre completo" required>
      <input type="email" name="email" class="tg-input" placeholder="Correo electrónico" required>
      <input type="password" name="password" class="tg-input" placeholder="Contraseña" required>
      <select name="plan" class="tg-input" required>
        <option value="">Selecciona tu plan</option>
        <option value="Basico">Básico - $299/mes</option>
        <option value="Pro">Pro - $499/mes</option>
        <option value="Elite">Elite - $799/mes</option>
      </select>
      <button type="submit" class="tg-btn">Crear Cuenta</button>
      <div class="tg-toggle">
        ¿Ya tienes cuenta? <a onclick="toggleAuth()">Inicia sesión</a>
      </div>
    </form>
  </div>
</div>
<?php else: ?>

<div id="dashboard-section">
  <div class="tg-header">
    <h1>⚡ TITAN GOLD GYM</h1>
    <p>Forjando campeones desde 2024</p>
  </div>
  <div class="tg-nav">
    <button class="tg-nav-btn active" onclick="showSection('overview')">Resumen</button>
    <button class="tg-nav-btn" onclick="showSection('routine')">Rutina</button>
    <button class="tg-nav-btn" onclick="showSection('diet')">Alimentación</button>
    <button class="tg-nav-btn" onclick="showSection('schedule')">Horario</button>
    <button class="tg-nav-btn" onclick="showSection('payments')">Pagos</button>
    <a href="logout.php" class="tg-nav-btn" style="text-decoration:none; text-align:center;">Salir</a>
  </div>
  <div class="tg-content">
    <div id="sec-overview" class="tg-section active">
      <div class="tg-card">
        <div class="tg-user-header">
          <div class="tg-avatar"><?= strtoupper(substr($user['nombre'], 0, 1)) ?></div>
          <div class="tg-user-info">
            <h3><?= htmlspecialchars($user['nombre']) ?></h3>
            <p><?= htmlspecialchars($user['email']) ?></p>
            <span class="tg-badge">Plan <?= htmlspecialchars($user['plan']) ?></span>
          </div>
        </div>
      </div>
      <div class="tg-grid">
        <div class="tg-card">
          <div class="tg-card-title">💪 Tu Coach</div>
          <div class="tg-info-box">
            <label>Entrenador Asignado</label>
            <div class="value"><?= htmlspecialchars($user['coach']) ?></div>
          </div>
          <div class="coach-desc"><?= htmlspecialchars($user['coach_desc']) ?></div>
        </div>
        <div class="tg-card">
          <div class="tg-card-title">📋 Rutina Actual</div>
          <div class="tg-info-box">
            <label>Programa</label>
            <div class="value"><?= htmlspecialchars($userRoutine['name']) ?></div>
          </div>
          <div class="progress-wrap">
            <label>Progreso Semanal</label>
            <div class="tg-progress-bar"><div class="tg-progress-fill" style="width:60%"></div></div>
            <div class="progress-text">3 de 5 días completados</div>
          </div>
        </div>
        <div class="tg-card">
          <div class="tg-card-title">💳 Estado de Pago</div>
          <div class="tg-info-box">
            <label>Plan</label>
            <div class="value">Plan <?= htmlspecialchars($user['plan']) ?> - $<?= htmlspecialchars($user['monto']) ?>/mes</div>
          </div>
          <div class="status-wrap">
            <label>Estado</label>
            <div><span class="tg-status paid"><?= htmlspecialchars($user['estado_pago']) ?></span></div>
          </div>
          <div class="payment-due">Próximo pago: <?= date('d/m/Y', strtotime($user['fecha_vencimiento'])) ?></div>
        </div>
      </div>
    </div>

    <div id="sec-routine" class="tg-section">
      <div class="tg-card">
        <div class="tg-card-title">🏋️ <span><?= htmlspecialchars($userRoutine['name']) ?></span></div>
        <p class="routine-desc"><?= htmlspecialchars($userRoutine['desc']) ?></p>
        <ul class="tg-routine-list">
          <?php foreach ($userRoutine['steps'] as $i => $step): ?>
            <li><span class="step-num"><?= $i + 1 ?></span><span><?= htmlspecialchars($step) ?></span></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <div id="sec-diet" class="tg-section">
      <div class="tg-card">
        <div class="tg-card-title">🥗 Plan de Alimentación</div>
        <p class="diet-intro">Meta calórica diaria: <strong><?= $userCals ?> kcal</strong></p>
        <div>
          <?php foreach ($userDiet as $meal): ?>
            <div class="tg-meal">
              <span class="tg-meal-time"><?= htmlspecialchars($meal['time']) ?></span>
              <span class="tg-meal-desc"><?= htmlspecialchars($meal['desc']) ?></span>
              <span class="tg-meal-cals"><?= htmlspecialchars($meal['cals']) ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <div id="sec-schedule" class="tg-section">
      <div class="tg-card">
        <div class="tg-card-title">📅 Horario Semanal</div>
        <p class="schedule-intro">Tus días de entrenamiento están resaltados en dorado.</p>
        <div class="tg-schedule">
          <div class="tg-day active"><div class="tg-day-name">Lun</div><div class="tg-day-hours">6:00 PM</div></div>
          <div class="tg-day active"><div class="tg-day-name">Mar</div><div class="tg-day-hours">6:00 PM</div></div>
          <div class="tg-day active"><div class="tg-day-name">Mié</div><div class="tg-day-hours">6:00 PM</div></div>
          <div class="tg-day active"><div class="tg-day-name">Jue</div><div class="tg-day-hours">6:00 PM</div></div>
          <div class="tg-day active"><div class="tg-day-name">Vie</div><div class="tg-day-hours">6:00 PM</div></div>
          <div class="tg-day"><div class="tg-day-name">Sáb</div><div class="tg-day-hours">Descanso</div></div>
          <div class="tg-day"><div class="tg-day-name">Dom</div><div class="tg-day-hours">Descanso</div></div>
        </div>
      </div>
    </div>

    <div id="sec-payments" class="tg-section">
      <div class="tg-card">
        <div class="tg-card-title">💰 Historial de Pagos</div>
        <table class="tg-table">
          <thead><tr><th>Fecha</th><th>Concepto</th><th>Monto</th><th>Estado</th></tr></thead>
          <tbody>
            <tr>
              <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
              <td>Inscripción Plan <?= htmlspecialchars($user['plan']) ?></td>
              <td style="color:var(--tg-yellow-bright); font-weight:600;">$<?= htmlspecialchars($user['monto']) ?></td>
              <td><span class="tg-status paid"><?= htmlspecialchars($user['estado_pago']) ?></span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<script>
function toggleAuth() {
  document.getElementById('login-form').classList.toggle('hidden');
  document.getElementById('register-form').classList.toggle('hidden');
}
function showSection(id) {
  document.querySelectorAll('.tg-section').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.tg-nav-btn').forEach(b => b.classList.remove('active'));
  const sec = document.getElementById('sec-' + id);
  if (sec) sec.classList.add('active');
  event.currentTarget.classList.add('active');
}
</script>
</body>
</html>
