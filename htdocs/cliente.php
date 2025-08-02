<?php
session_start();
$_SESSION['usuario'] = 'cliente_anonimo';
$_SESSION['nivel'] = 'cliente';
header("Location: cliente_interface.php");
exit;
