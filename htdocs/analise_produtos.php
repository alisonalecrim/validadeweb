<?php 
require_once 'classes/Database.php';
require_once 'classes/User.php';

$db = new Database();
$user = new User($db);

if (!$user->checkAccess('admin')) {
    $user->redirect('index.html');
}

// Consultar produtos lidos
$stmt = $db->getPdo()->query("SELECT codigo, codigo_interno, descricao, quantidade, validade FROM produtos_lidos");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verificar produtos no DONV usando cod_produto
$donvProdutos = [];
$donvStmt = $db->getPdo()->query("SELECT cod_produto, dta_validade, qtd_contada FROM donv");
$donvData = $donvStmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($donvData as $donvItem) {
    $donvProdutos[$donvItem['cod_produto']] = [
        'dta_validade' => $donvItem['dta_validade'],
        'qtd_contada' => $donvItem['qtd_contada'] ?? 'N/A'
    ];
}

// Exportar CSV
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'export_csv') {
    $selected = $_POST['selected'] ?? [];

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="analise_produtos_lidos.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Código', 'Código Interno', 'Descrição', 'Quantidade', 'Validade', 'Lançado no DONV', 'Data de Validade', 'Quantidade no DONV'], ';');

    foreach ($produtos as $produto) {
        if (in_array($produto['codigo_interno'], $selected)) {
            $codigoInterno = $produto['codigo_interno'];
            $lanhado = isset($donvProdutos[$codigoInterno]) ? 'Sim' : 'Não';
            $dta_validade = $donvProdutos[$codigoInterno]['dta_validade'] ?? 'N/A';
            $qtd_contada = $donvProdutos[$codigoInterno]['qtd_contada'] ?? 'N/A';

            // Substituir ponto por vírgula nas casas decimais da quantidade no DONV
            if (is_numeric($qtd_contada)) {
                $qtd_contada = str_replace('.', ',', $qtd_contada);
            }

            fputcsv($output, [
                $produto['codigo'],
                $codigoInterno,
                $produto['descricao'],
                $produto['quantidade'],
                $produto['validade'] ?? 'N/A',
                $lanhado,
                $dta_validade,
                $qtd_contada
            ], ';');
        }
    }
    fclose($output);
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Análise de Produtos Lidos - Paradão da Validade WEB</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f0f8ff;
      padding: 30px;
      margin: 0;
    }
    .container {
      max-width: 1000px;
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
    .controls {
      margin-bottom: 20px;
      text-align: center;
    }
    .btn {
      padding: 10px 20px;
      margin: 0 10px;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      background-color: #004AAD;
      color: white;
    }
    .btn:hover {
      background-color: #00307a;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: center;
    }
    th {
      background-color: #004AAD;
      color: white;
    }
    td input[type="checkbox"] {
      margin: 0;
      vertical-align: middle;
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
  <h2>Análise de Produtos Lidos</h2>

  <div class="controls">
    <button type="button" class="btn" onclick="filtrar('Sim')">Filtrar Sim</button>
    <button type="button" class="btn" onclick="filtrar('Não')">Filtrar Não</button>
  </div>

  <form method="post" id="mainForm">
    <input type="hidden" name="action" value="export_csv">
    <table>
      <thead>
        <tr>
          <th><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
          <th>Código</th>
          <th>Código Interno</th>
          <th>Descrição</th>
          <th>Quantidade</th>
          <th>Validade</th>
          <th>Lançado no DONV</th>
          <th>Data de Validade</th>
          <th>Quantidade no DONV</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($produtos as $produto): 
          $codigoInterno = $produto['codigo_interno'];
          $lanhado = isset($donvProdutos[$codigoInterno]);
        ?>
        <tr>
          <td><input type="checkbox" name="selected[]" value="<?= htmlspecialchars($codigoInterno) ?>"></td>
          <td><?= htmlspecialchars($produto['codigo']) ?></td>
          <td><?= htmlspecialchars($codigoInterno) ?></td>
          <td><?= htmlspecialchars($produto['descricao']) ?></td>
          <td><?= htmlspecialchars($produto['quantidade']) ?></td>
          <td><?= htmlspecialchars($produto['validade'] ?? 'N/A') ?></td>
          <td class="lanhado"><?= $lanhado ? 'Sim' : 'Não' ?></td>
          <td><?= $lanhado ? htmlspecialchars($donvProdutos[$codigoInterno]['dta_validade']) : 'N/A' ?></td>
          <td><?= $lanhado ? htmlspecialchars($donvProdutos[$codigoInterno]['qtd_contada']) : 'N/A' ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <div class="controls">
      <button type="submit" class="btn">Exportar CSV</button>
    </div>
  </form>

  <footer>© <?= date("Y") ?> Paradão da Validade WEB 2025 - Desenvolvido por Alison Guilherme</footer>
</div>

<script>
function toggleSelectAll(checkbox) {
  const checkboxes = document.querySelectorAll('input[name="selected[]"]');
  checkboxes.forEach(cb => cb.checked = checkbox.checked);
}

function filtrar(valor) {
  const linhas = document.querySelectorAll("tbody tr");
  linhas.forEach(linha => {
    const celula = linha.querySelector(".lanhado");
    linha.style.display = (celula.textContent.trim() === valor) ? "" : "none";
  });
}
</script>
</body>
</html>
