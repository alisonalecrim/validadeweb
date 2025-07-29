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
  <title>Importar Produtos - Paradão da Validade WEB</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f0f8ff;
      padding: 30px;
      margin: 0;
    }
    .container {
      max-width: 900px;
      margin: auto;
      background-color: #fff;
      padding: 25px 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    h2 {
      text-align: center;
      color: #004AAD;
      margin-bottom: 20px;
    }
    input[type="file"] {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
    .btn {
      padding: 10px 20px;
      margin-top: 15px;
      margin-right: 10px;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
    }
    .btn-blue {
      background-color: #004AAD;
      color: white;
    }
    .btn-blue:hover {
      background-color: #00307a;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 25px;
    }
    table, th, td {
      border: 1px solid #ccc;
    }
    th {
      background-color: #004AAD;
      color: white;
      padding: 10px;
    }
    td {
      padding: 10px;
      text-align: center;
    }
    #botoes {
      margin-top: 20px;
      text-align: right;
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
  <h2>Importar Planilha de Produtos (CSV)</h2>
  <input type="file" id="csvFile" accept=".csv">
  <div id="botoes">
    <button class="btn btn-blue" onclick="processarCSV()">Visualizar Dados</button>
    <button class="btn btn-blue" onclick="salvarNoBanco()">Salvar no Banco</button>
    <a href="exportar.php" target="_blank">
      <button class="btn btn-blue">Exportar Produtos Lidos</button>
    </a>
  </div>
  <table id="tabela-dados" style="display: none;">
    <thead>
      <tr>
        <th>Código Interno</th>
        <th>Descrição</th>
        <th>Código de Barras</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
  <footer>© <?= date("Y") ?> Paradão da Validade WEB Desenvolvido por Alison Guilherme</footer>
</div>

<script>
let dadosImportados = [];

function processarCSV() {
  const fileInput = document.getElementById('csvFile');
  const file = fileInput.files[0];
  if (!file) {
    alert("Selecione um arquivo CSV.");
    return;
  }
  const reader = new FileReader();
  reader.onload = function(e) {
    const linhas = e.target.result.split(/\r?\n/);
    const tabela = document.getElementById('tabela-dados');
    const tbody = tabela.querySelector('tbody');
    tbody.innerHTML = '';
    dadosImportados = [];
    linhas.forEach((linha, index) => {
      const colunas = linha.split(';');
      if (colunas.length >= 3 && index > 0) {
        let interno = colunas[0].replace(/"/g, '').trim();
        let descricao = colunas[1].replace(/"/g, '').trim();
        let codigo = colunas[2].replace(/"/g, '').trim();
        if (codigo && descricao && interno) {
          dadosImportados.push({ codigo_interno: interno, descricao, codigo });
          const tr = document.createElement('tr');
          tr.innerHTML = `<td>${interno}</td><td>${descricao}</td><td>${codigo}</td>`;
          tbody.appendChild(tr);
        }
      }
    });
    tabela.style.display = 'table';
  };
  reader.readAsText(file, 'utf-8');
}

function salvarNoBanco() {
  if (dadosImportados.length === 0) {
    alert("Nenhum dado para salvar.");
    return;
  }
  if (!confirm("Deseja realmente salvar os produtos no banco de dados?")) return;
  fetch('salvar_produtos.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(dadosImportados)
  })
  .then(res => res.text())
  .then(data => {
    alert(data);
    location.reload();
  })
  .catch(err => {
    alert("Erro ao salvar no banco.");
    console.error(err);
  });
}
</script>
</body>
</html>