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
  <title>Importar DONV - Paradão da Validade WEB</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f0f8ff;
      padding: 30px;
      margin: 0;
    }
    .container {
      max-width: 1200px;
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
      font-size: 0.9em;
    }
    td {
      padding: 10px;
      text-align: center;
      font-size: 0.9em;
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
  <h2>Importar Planilha DONV (CSV)</h2>
  <?php if (isset($_GET['importado'])): ?>
    <div class="success">Dados DONV importados com sucesso!</div>
  <?php endif; ?>
  <input type="file" id="csvFile" accept=".csv">
  <div id="botoes">
    <form method="post" action="importar_donv.php" enctype="multipart/form-data" id="importForm" style="display: inline;">
      <input type="hidden" name="fileData" id="fileData">
      <button type="submit" class="btn btn-blue" onclick="prepareImport()">Importar Direto</button>
    </form>
    <button class="btn btn-blue" onclick="processarCSV()">Visualizar Dados</button>
    <button class="btn btn-blue" onclick="salvarNoBanco()" id="saveButton" style="display: none;">Salvar no Banco</button>
  </div>
  <table id="tabela-dados" style="display: none;">
    <thead>
      <tr>
        <th>Loja</th>
        <th>Cód. Produto</th>
        <th>Descrição</th>
        <th>Qtd Contada</th>
        <th>Estq Vencer</th>
        <th>Emb</th>
        <th>Dta Validade</th>
        <th>Dta Expiração</th>
        <th>Dta Inclusão</th>
        <th>Status</th>
        <th>Comentário Comercial</th>
        <th>Usuário Inclusão</th>
        <th>Usuário Alteração</th>
        <th>Dta Enc/Can</th>
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
      if (colunas.length >= 14 && index > 0) {
        let loja = colunas[0].replace(/"/g, '').trim();
        let cod_produto = colunas[1].replace(/"/g, '').trim();
        let descricao = colunas[2].replace(/"/g, '').trim();
        let qtd_contada = colunas[3].replace(/"/g, '').trim();
        let estq_vencer = colunas[4].replace(/"/g, '').trim();
        let emb = colunas[5].replace(/"/g, '').trim();
        let dta_validade = colunas[6].replace(/"/g, '').trim();
        let dta_expiracao = colunas[7].replace(/"/g, '').trim();
        let dta_inclusao = colunas[8].replace(/"/g, '').trim();
        let status = colunas[9].replace(/"/g, '').trim();
        let comentario_comercial = colunas[10].replace(/"/g, '').trim();
        let usuario_inclusao = colunas[11].replace(/"/g, '').trim();
        let usuario_alteracao = colunas[12].replace(/"/g, '').trim();
        let dta_enc_can = colunas[13].replace(/"/g, '').trim();
        if (loja && cod_produto && descricao && qtd_contada && estq_vencer && emb && dta_validade && dta_expiracao && dta_inclusao && status && usuario_inclusao) {
          dadosImportados.push({
            loja, cod_produto, descricao, qtd_contada, estq_vencer, emb,
            dta_validade, dta_expiracao, dta_inclusao, status, comentario_comercial,
            usuario_inclusao, usuario_alteracao, dta_enc_can
          });
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${loja}</td>
            <td>${cod_produto}</td>
            <td>${descricao}</td>
            <td>${qtd_contada}</td>
            <td>${estq_vencer}</td>
            <td>${emb}</td>
            <td>${dta_validade}</td>
            <td>${dta_expiracao}</td>
            <td>${dta_inclusao}</td>
            <td>${status}</td>
            <td>${comentario_comercial}</td>
            <td>${usuario_inclusao}</td>
            <td>${usuario_alteracao}</td>
            <td>${dta_enc_can}</td>
          `;
          tbody.appendChild(tr);
        }
      }
    });
    tabela.style.display = 'table';
    document.getElementById('saveButton').style.display = 'inline';
  };
  reader.readAsText(file, 'utf-8');
}

function salvarNoBanco() {
  if (dadosImportados.length === 0) {
    alert("Nenhum dado para salvar.");
    return;
  }
  if (!confirm("Deseja realmente salvar os dados no banco de dados?")) return;
  fetch('salvar_donv.php', {
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

function prepareImport() {
  const fileInput = document.getElementById('csvFile');
  if (!fileInput.files[0]) {
    alert("Selecione um arquivo CSV.");
    return false;
  }
  // Simula o envio do formulário com o arquivo
  document.getElementById('fileData').value = fileInput.files[0].name; // Apenas para rastreamento, o arquivo é enviado via enctype
  return true;
}
</script>
</body>
</html>