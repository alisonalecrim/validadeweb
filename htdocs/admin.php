<?php
require_once 'classes/Database.php';
require_once 'classes/User.php';

$db = new Database();
$user = new User($db);

if (!$user->checkAccess('admin')) {
    $user->redirect('index.html');
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Menu Administrador - Paradão da Validade WEB</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f0f8ff;
      padding: 30px;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }
    .container {
      max-width: 600px;
      margin: auto;
      background-color: #fff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      text-align: center;
    }
    h2 {
      color: #004AAD;
      margin-bottom: 30px;
    }
    .menu-item {
      margin: 15px 0;
    }
    .btn {
      padding: 12px 25px;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
      width: 80%;
      max-width: 300px;
    }
    .btn-blue {
      background-color: #004AAD;
      color: white;
    }
    .btn-blue:hover {
      background-color: #00307a;
    }
    .btn-yellow {
      background-color: #FFD500;
      color: black;
    }
    .btn-yellow:hover {
      background-color: #e6c800;
    }
    .btn-green {
      background-color: #28a745;
      color: white;
    }
    .btn-green:hover {
      background-color: #1e7e34;
    }
    footer {
      text-align: center;
      margin-top: 40px;
      font-size: 0.9em;
      color: #666;
    }
  </style>
</head>
<body>
<div class="container">
  <h2>Menu Administrador</h2>
  <div class="menu-item">
    <a href="importar_produtos.php" class="btn btn-blue">Importar Planilha de Produtos</a>
  </div>
  <div class="menu-item">
    <a href="donv.php" class="btn btn-yellow">Importar Planilha DONV</a>
  </div>
  <div class="menu-item">
    <a href="analise_produtos.php" class="btn btn-green">Análise de Produtos Lidos</a>
  </div>
  <footer>© <?= date("Y") ?> Paradão da Validade WEB Desenvolvido por Alison Guilherme</footer>
</div>
</body>
</html>