<?php
require_once 'classes/Database.php';
require_once 'classes/CsvHandler.php';

$db = new Database();
$csvHandler = new CsvHandler($db);

if ($_FILES['arquivo']['error'] == UPLOAD_ERR_OK) {
    try {
        $csvHandler->import($_FILES['arquivo']['tmp_name']);
        header("Location: importar_produtos.php?importado=1");
    } catch (Exception $e) {
        die("Erro no upload: " . $e->getMessage());
    }
} else {
    die("Erro no upload do arquivo.");
}
?>