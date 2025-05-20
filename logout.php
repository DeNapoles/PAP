<?php
session_start();
session_destroy();

// Enviar um cookie para indicar que o logout foi feito
setcookie('logout', 'true', time() + 3600, '/');

header('Location: index.php');
exit;
?> 