<?php
class ScannedProduct {
    private $pdo;

    public function __construct(Database $db) {
        $this->pdo = $db->getPdo();
    }

    public function save($data) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO produtos_lidos (codigo, codigo_interno, descricao, quantidade, validade) 
             VALUES (?, ?, ?, ?, ?) 
             ON DUPLICATE KEY UPDATE codigo_interno = VALUES(codigo_interno), descricao = VALUES(descricao), quantidade = quantidade + VALUES(quantidade), validade = VALUES(validade)"
        );
        $stmt->execute([$data['codigo'], $data['codigo_interno'], $data['descricao'], $data['quantidade'], $data['validade']]);
    }
}
?>