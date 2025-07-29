<?php
require_once 'classes/Database.php';
require_once 'classes/Product.php';

$db = new Database();
$product = new Product($db);

header('Content-Type: application/json');

$codigo = $_GET['codigo'] ?? '';

if ($codigo) {
    $result = $product->findByBarcode($codigo);
    if ($result) {
        echo json_encode([
            'sucesso' => true,
            'codigo_interno' => $result['codigo_interno'],
            'descricao' => $result['descricao']
        ]);
    } else {
        echo json_encode(['sucesso' => false]);
    }
} else {
    echo json_encode(['sucesso' => false, 'message' => 'Código de barras não fornecido']);
}
?>