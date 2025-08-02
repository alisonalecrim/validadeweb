<?php
session_start();
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (!empty($usuario) && !empty($senha)) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
        $stmt->execute([$usuario]);
        $usuarioDB = $stmt->fetch();

        // Comparação direta sem hash
        if ($usuarioDB && $usuarioDB['senha'] === $senha) {
            $_SESSION['usuario'] = $usuarioDB['usuario'];
            $_SESSION['nivel'] = $usuarioDB['nivel'];

            if ($usuarioDB['nivel'] === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: cliente_interface.php");
            }
            exit;
        } else {
            $erro = "Usuário ou senha inválidos.";
        }
    } else {
        $erro = "Preencha todos os campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
  <meta charset="UTF-8">
  <title>Login - Paradão da Validade WEB</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      background: #f0f8ff;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }

    .login-box {
      background: #fff;
      padding: 25px 30px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      width: 90%;
      max-width: 400px;
    }

    h2 {
      text-align: center;
      color: #004AAD;
      margin-bottom: 20px;
    }

    input[type="text"], input[type="password"] {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    button {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      background-color: #004AAD;
      color: #fff;
    }

    button:hover {
      background-color: #00337f;
    }

    .cliente-btn {
      background-color: #FFD500;
      color: #000;
    }

    .cliente-btn:hover {
      background-color: #e6c800;
    }

    .erro {
      color: red;
      text-align: center;
      margin-top: 10px;
    }
  </style>
</head>
<body>

<div class="login-box">
  <h2>Paradão da Validade WEB</h2>

  <?php if (isset($erro)) echo "<div class='erro'>$erro</div>"; ?>

  <form method="post">
    <input type="text" name="usuario" placeholder="Usuário" required>
    <input type="password" name="senha" placeholder="Senha" required>
    <button type="submit">Entrar</button>
  </form>

  <form action="cliente_interface.php" method="get">
    <button type="submit" class="cliente-btn">Entrar como Cliente</button>
  </form>
</div>

</body>
</html>
