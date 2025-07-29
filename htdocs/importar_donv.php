<?php
require_once 'classes/Database.php';
require_once 'classes/CsvHandler.php';

$db = new Database();
$csvHandler = new CsvHandler($db);

if ($_FILES['arquivo']['error'] == UPLOAD_ERR_OK) {
    try {
        $csvHandler->importDonv($_FILES['arquivo']['tmp_name']);
        header("Location: donv.php?importado=1");
    } catch (Exception $e) {
        die("Erro no upload: " . $e->getMessage());
    }
} else {
    die("Erro no upload do arquivo.");
}
?>