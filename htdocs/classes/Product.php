<?php
class Product {
    private $pdo;

    public function __construct(Database $db) {
        $this->pdo = $db->getPdo();
    }

    public function findByBarcode($barcode) {
    $stmt = $this->pdo->prepare("SELECT codigo_interno, codigo, descricao FROM produtos WHERE codigo = ? LIMIT 1");
    $stmt->execute([$barcode]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function saveProducts($products) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO produtos (codigo_interno, codigo, descricao) VALUES (?, ?, ?) 
             ON DUPLICATE KEY UPDATE descricao = VALUES(descricao), codigo_interno = VALUES(codigo_interno)"
        );

        foreach ($products as $product) {
            $internalCode = trim($product['codigo_interno'] ?? '');
            $code = trim($product['codigo'] ?? '');
            $description = trim($product['descricao'] ?? '');
            if (!empty($code) && !empty($description) && !empty($internalCode)) {
                $stmt->execute([$internalCode, $code, $description]);
            }
        }
        return true;
    }
}
?>