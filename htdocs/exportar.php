<?php
require_once 'classes/Database.php';
require_once 'classes/CsvHandler.php';

$db = new Database();
$csvHandler = new CsvHandler($db);
$csvHandler->export();
?>