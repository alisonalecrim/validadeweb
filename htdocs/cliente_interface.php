<?php
require_once 'classes/Database.php';
require_once 'classes/User.php';

$db = new Database();
$user = new User($db);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Paradão da Validade WEB - Cliente</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <style>
    body {
      margin: 0; padding: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4faff;
      display: flex; align-items: center; justify-content: center;
      min-height: 100vh;
    }
    .container {
      background-color: #fff;
      padding: 25px 30px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      width: 90%; max-width: 420px;
    }
    .title {
      text-align: center;
      font-size: 1.6em;
      font-weight: bold;
      margin-bottom: 15px;
      color: #004AAD;
    }
    #reader {
      width: 100%;
      margin-bottom: 20px;
      border: 2px solid #FFD500;
      border-radius: 8px;
    }
    label {
      display: block;
      margin-top: 10px;
      font-weight: 600;
    }
    input[type="text"], input[type="number"], input[type="date"] {
      width: 100%;
      padding: 8px 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 1em;
    }
    .btn {
      background-color: #004AAD;
      color: #fff;
      border: none;
      padding: 10px;
      width: 100%;
      border-radius: 6px;
      font-size: 1em;
      cursor: pointer;
      margin-top: 15px;
    }
    .btn:hover {
      background-color: #00337f;
    }
    footer {
      text-align: center;
      margin-top: 20px;
      font-size: 0.9em;
      color: #888;
    }
    .success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 10px;
      text-align: center;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="title">Paradão da Validade WEB</div>
  <?php if (isset($_GET['sucesso'])): ?>
    <div class="success">Produto salvo com sucesso!</div>
  <?php endif; ?>
  <div id="reader"></div>
  <form method="post" action="salvar.php">
    <label for="barcode">Código de Barras</label>
    <input type="number" id="barcode" name="barcode" required>
    <label for="codigo_interno">Código Interno</label>
    <input type="text" id="codigo_interno" name="codigo_interno" readonly>
    <label for="descricao">Descrição</label>
    <input type="text" id="descricao" name="descricao" required>
    <label for="quantidade">Quantidade</label>
    <input type="number" id="quantidade" name="quantidade" min="1" required>
    <label for="validade">Data de Validade</label>
    <input type="date" id="validade" name="validade" required>
    <button type="submit" class="btn">Salvar</button>
  </form>
  <footer>© 2025 Paradão da Validade WEB Desenvolvido por Alison Guilherme</footer>
</div>

<audio id="bip-sound" src="https://cdn.pixabay.com/download/audio/2022/03/15/audio_69e52a729b.mp3?filename=beep-5-96243.mp3" preload="auto"></audio>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
  const buscarDescricao = (codigo) => {
    fetch('buscar.php?codigo=' + encodeURIComponent(codigo))
      .then(response => response.json())
      .then(data => {
        const codigoInternoInput = document.getElementById('codigo_interno');
        const descricaoInput = document.getElementById('descricao');
        if (data.sucesso) {
          codigoInternoInput.value = data.codigo_interno || '';
          descricaoInput.value = data.descricao || '';
          codigoInternoInput.readOnly = true;
          descricaoInput.readOnly = true;
        } else {
          codigoInternoInput.value = '';
          descricaoInput.value = '';
          codigoInternoInput.readOnly = false;
          descricaoInput.readOnly = false;
          codigoInternoInput.placeholder = 'Código interno não encontrado';
          descricaoInput.placeholder = 'Descrição não encontrada';
        }
      })
      .catch(error => console.error('Erro ao buscar:', error));
  };

  function iniciarLeitor() {
    const html5QrCode = new Html5Qrcode("reader");
    const config = {
      fps: 10,
      qrbox: { width: 250, height: 100 },
      aspectRatio: 1.5
    };
    html5QrCode.start(
      { facingMode: "environment" },
      config,
      (decodedText, decodedResult) => {
        const barcodeField = document.getElementById("barcode");
        barcodeField.value = decodedText;
        document.getElementById("bip-sound").play();
        buscarDescricao(decodedText);
        html5QrCode.stop().then(() => {
          document.getElementById("reader").innerHTML = "<em>Leitura concluída</em>";
        });
      },
      (errorMessage) => {}
    ).catch((err) => {
      console.error("Erro ao iniciar câmera:", err);
    });
  }

  window.onload = () => {
    iniciarLeitor();
    document.getElementById("barcode").addEventListener("change", (e) => {
      const codigo = e.target.value;
      if (codigo.length >= 8) {
        buscarDescricao(codigo);
      }
    });
  };
  // Define limite máximo e mínimo para a validade
  function limitarValidadeMaxima() {
    const validadeInput = document.getElementById("validade");
    const hoje = new Date();
    const maxDate = new Date();
    maxDate.setDate(hoje.getDate() + 60);

    const formatarData = (data) => {
      const ano = data.getFullYear();
      const mes = String(data.getMonth() + 1).padStart(2, '0');
      const dia = String(data.getDate()).padStart(2, '0');
      return `${ano}-${mes}-${dia}`;
    };

    validadeInput.setAttribute("min", formatarData(hoje));
    validadeInput.setAttribute("max", formatarData(maxDate));
  }

  // Verifica se a validade está dentro do intervalo permitido
  function validarDataValidade(event) {
    const validadeInput = document.getElementById("validade");
    const valor = validadeInput.value;
    if (!valor) return; // Se estiver vazio, deixe o HTML5 cuidar disso

    const hoje = new Date();
    const validade = new Date(valor);
    const limite = new Date();
    limite.setDate(hoje.getDate() + 60);

    if (validade > limite) {
      alert("A data de validade não pode ser superior a 60 dias a partir de hoje.");
      event.preventDefault(); // Impede o envio do formulário
    }
  }

  window.onload = () => {
    iniciarLeitor();
    limitarValidadeMaxima();

    document.getElementById("barcode").addEventListener("change", (e) => {
      const codigo = e.target.value;
      if (codigo.length >= 8) {
        buscarDescricao(codigo);
      }
    });

    // Validação antes de enviar
    const form = document.querySelector("form");
    form.addEventListener("submit", validarDataValidade);
  };
</script>
</body>
</html>