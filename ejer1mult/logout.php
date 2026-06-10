<?php
require_once __DIR__ . '/includes/functions.php';

registrarFlujo('Cierre de sesión', 'El usuario salió del sistema.', 'General', 'Completado');
$_SESSION = [];
session_destroy();

header('Location: login.php?mensaje=' . urlencode('Sesión cerrada correctamente.'));
exit;
