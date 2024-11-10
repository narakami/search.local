<?php
session_start();
session_unset(); // Удаляет все переменные сессии
session_destroy(); // Уничтожает сессию

echo json_encode(['status' => 'logged_out']);
?>
