<?php
$host = 'sql308.infinityfree.com';
$dbname = 'if0_39055940_validade';
$user = 'if0_39055940';
$pass = '74198842';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
