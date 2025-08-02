<?php
require_once 'classes/Database.php';
require_once 'classes/ScannedProduct.php';

$db = new Database();
$scannedProduct = new ScannedProduct($db);

$barcode = $_POST['barcode'] ?? '';
$codigo_interno = $_POST['codigo_interno'] ?? '';
$descricao = $_POST['descricao'] ?? '';
$quantidade = $_POST['quantidade'] ?? 1;
$validade = $_POST['validade'] ?? null;

if ($barcode && $descricao) {
    try {
        $scannedProduct->save([
            'codigo' => $barcode,
            'codigo_interno' => $codigo_interno,
            'descricao' => $descricao,
            'quantidade' => $quantidade,
            'validade' => $validade
        ]);
        header("Location: cliente_interface.php?sucesso=1");
        exit;
    } catch (Exception $e) {
        http_response_code(500);
        echo "Erro ao salvar o produto: " . $e->getMessage();
    }
} else {
    http_response_code(400);
    echo "Dados inválidos. Código de barras e descrição são obrigatórios.";
}
?>