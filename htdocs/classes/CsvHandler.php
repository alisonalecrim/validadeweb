<?php
class CsvHandler {
    private $pdo;

    public function __construct(Database $db) {
        $this->pdo = $db->getPdo();
    }

    public function export() {
        header('Content-Type: text/csv; charset=ISO-8859-1');
        header('Content-Disposition: attachment; filename=relatorio_validades.csv');

        $output = fopen('php://output', 'w');
        stream_filter_append($output, 'convert.iconv.UTF-8/ISO-8859-1');
        fputcsv($output, ['Código de barras', 'Descrição do produto', 'Quantidade', 'Validade'], ';');

        $stmt = $this->pdo->query("SELECT codigo, descricao, quantidade, validade FROM produtos_lidos ORDER BY validade");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, [
                $row['codigo'],
                $row['descricao'],
                $row['quantidade'],
                $row['validade']
            ], ';');
        }
        fclose($output);
        exit;
    }

    public function import($fileTmpPath) {
        $file = fopen($fileTmpPath, 'r');
        if ($file === false) {
            throw new Exception("Não foi possível abrir o arquivo CSV.");
        }

        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO produtos (codigo_interno, codigo, descricao) VALUES (?, ?, ?) 
                 ON DUPLICATE KEY UPDATE descricao = VALUES(descricao), codigo_interno = VALUES(codigo_interno)"
            );

            $isFirstRow = true;
            while (($row = fgetcsv($file, 0, ';', '"')) !== false) {
                if ($isFirstRow) {
                    $isFirstRow = false;
                    continue; // Pula o cabeçalho
                }

                $internalCode = trim($row[0] ?? '');
                $description = trim($row[1] ?? '');
                $code = trim($row[2] ?? '');
                if ($internalCode && $code && $description) {
                    $stmt->execute([$internalCode, $code, $description]);
                }
            }

            fclose($file);
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            fclose($file);
            throw new Exception("Erro ao importar: " . $e->getMessage());
        }
    }

    public function importDonv($fileTmpPath) {
        $file = fopen($fileTmpPath, 'r');
        if ($file === false) {
            throw new Exception("Não foi possível abrir o arquivo CSV.");
        }

        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO donv (loja, cod_produto, descricao, qtd_contada, estq_vencer, emb, 
                                  dta_validade, dta_expiracao, dta_inclusao, status, 
                                  comentario_comercial, usuario_inclusao, usuario_alteracao, dta_enc_can) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $isFirstRow = true;
            while (($row = fgetcsv($file, 0, ';', '"')) !== false) {
                if ($isFirstRow) {
                    $isFirstRow = false;
                    continue; // Pula o cabeçalho
                }

                $loja = trim($row[0] ?? '');
                $cod_produto = trim($row[1] ?? '');
                $descricao = trim($row[2] ?? '');
                $qtd_contada = trim($row[3] ?? '');
                $estq_vencer = trim($row[4] ?? '');
                $emb = trim($row[5] ?? '');
                $dta_validade = $this->convertDate(trim($row[6] ?? ''));
                $dta_expiracao = $this->convertDate(trim($row[7] ?? ''));
                $dta_inclusao = $this->convertDate(trim($row[8] ?? ''));
                $status = trim($row[9] ?? '');
                $comentario_comercial = trim($row[10] ?? '') ?: null;
                $usuario_inclusao = trim($row[11] ?? '');
                $usuario_alteracao = trim($row[12] ?? '') ?: null;
                $dta_enc_can = $this->convertDate(trim($row[13] ?? '')) ?: null;

                if ($loja && $cod_produto && $descricao && $qtd_contada && $estq_vencer && $emb && 
                    $dta_validade && $dta_expiracao && $dta_inclusao && $status && $usuario_inclusao) {
                    $stmt->execute([
                        $loja, $cod_produto, $descricao, $qtd_contada, $estq_vencer, $emb,
                        $dta_validade, $dta_expiracao, $dta_inclusao, $status,
                        $comentario_comercial, $usuario_inclusao, $usuario_alteracao, $dta_enc_can
                    ]);
                }
            }

            fclose($file);
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            fclose($file);
            throw new Exception("Erro ao importar: " . $e->getMessage());
        }
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