<?php
class Donv {
    private $pdo;

    public function __construct(Database $db) {
        $this->pdo = $db->getPdo();
    }

    public function saveRecords($records) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO donv (loja, cod_produto, descricao, qtd_contada, estq_vencer, emb, 
                              dta_validade, dta_expiracao, dta_inclusao, status, 
                              comentario_comercial, usuario_inclusao, usuario_alteracao, dta_enc_can) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        foreach ($records as $record) {
            $loja = trim($record['loja'] ?? '');
            $cod_produto = trim($record['cod_produto'] ?? '');
            $descricao = trim($record['descricao'] ?? '');
            $qtd_contada = trim($record['qtd_contada'] ?? '');
            $estq_vencer = trim($record['estq_vencer'] ?? '');
            $emb = trim($record['emb'] ?? '');
            $dta_validade = $this->convertDate(trim($record['dta_validade'] ?? ''));
            $dta_expiracao = $this->convertDate(trim($record['dta_expiracao'] ?? ''));
            $dta_inclusao = $this->convertDate(trim($record['dta_inclusao'] ?? ''));
            $status = trim($record['status'] ?? '');
            $comentario_comercial = trim($record['comentario_comercial'] ?? '') ?: null;
            $usuario_inclusao = trim($record['usuario_inclusao'] ?? '');
            $usuario_alteracao = trim($record['usuario_alteracao'] ?? '') ?: null;
            $dta_enc_can = $this->convertDate(trim($record['dta_enc_can'] ?? '')) ?: null;

            if ($loja && $cod_produto && $descricao && $qtd_contada && $estq_vencer && $emb && 
                $dta_validade && $dta_expiracao && $dta_inclusao && $status && $usuario_inclusao) {
                $stmt->execute([
                    $loja, $cod_produto, $descricao, $qtd_contada, $estq_vencer, $emb,
                    $dta_validade, $dta_expiracao, $dta_inclusao, $status,
                    $comentario_comercial, $usuario_inclusao, $usuario_alteracao, $dta_enc_can
                ]);
            }
        }
        return true;
    }

    private function convertDate($date) {
        if (!$date) return null;
        $parts = explode('/', $date);
        if (count($parts) === 3) {
            return sprintf('%04d-%02d-%02d', $parts[2], $parts[1], $parts[0]);
        }
        return null;
    }
}
?>