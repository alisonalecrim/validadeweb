<?php
require_once 'classes/Database.php';
require_once 'classes/Donv.php';

$db = new Database();
$donv = new Donv($db);

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !is_array($data)) {
    http_response_code(400);
    echo "Dados inválidos.";
    exit;
}

try {
    $donv->saveRecords($data);
    echo "Registros DONV importados com sucesso!";
} catch (Exception $e) {
    http_response_code(500);
    echo "Erro ao salvar: " . $e->getMessage();
}
?>