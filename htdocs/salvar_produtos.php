<?php
require_once 'classes/Database.php';
require_once 'classes/Product.php';

$db = new Database();
$product = new Product($db);

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !is_array($data)) {
    http_response_code(400);
    echo "Dados inválidos.";
    exit;
}

try {
    $product->saveProducts($data);
    echo "Produtos importados com sucesso!";
} catch (Exception $e) {
    http_response_code(500);
    echo "Erro ao salvar: " . $e->getMessage();
}
?>