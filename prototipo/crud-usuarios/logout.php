<?php
// usuarios/logout.php
session_start();
session_unset();
session_destroy();
header("Location: /prototipo/usuarios/login.php");
exit;
