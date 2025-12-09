<?php
require_once 'config.php';

// Adicionar nova transação
function adicionarTransacao($dados) {
    $conn = getConnection();
    
    // Validação
    if (empty($dados['tipo']) || empty($dados['valor']) || empty($dados['data']) || empty($dados['categoria'])) {
        return false;
    }
    
    // Validar tipo
    if (!in_array($dados['tipo'], ['receita', 'despesa'])) {
        return false;
    }
    
    // Validar valor
    $valor = floatval($dados['valor']);
    if ($valor <= 0) {
        return false;
    }
    
    try {
        $sql = "INSERT INTO transacoes (tipo, valor, data, categoria, descricao) 
                VALUES (:tipo, :valor, :data, :categoria, :descricao)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':tipo' => $dados['tipo'],
            ':valor' => $valor,
            ':data' => $dados['data'],
            ':categoria' => trim($dados['categoria']),
            ':descricao' => trim($dados['descricao'] ?? '')
        ]);
        
        return true;
    } catch (PDOException $e) {
        error_log("Erro ao adicionar transação: " . $e->getMessage());
        return false;
    }
}

// Buscar todas as transações
function buscarTransacoes() {
    $conn = getConnection();
    
    try {
        $sql = "SELECT * FROM transacoes ORDER BY data DESC, id DESC";
        $stmt = $conn->query($sql);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Erro ao buscar transações: " . $e->getMessage());
        return [];
    }
}

// Excluir transação
function excluirTransacao($id) {
    $conn = getConnection();
    
    try {
        $sql = "DELETE FROM transacoes WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return true;
    } catch (PDOException $e) {
        error_log("Erro ao excluir transação: " . $e->getMessage());
        return false;
    }
}

// Calcular totais
function calcularTotais() {
    $conn = getConnection();
    
    try {
        $sql = "SELECT 
                    SUM(CASE WHEN tipo = 'receita' THEN valor ELSE 0 END) as receitas,
                    SUM(CASE WHEN tipo = 'despesa' THEN valor ELSE 0 END) as despesas
                FROM transacoes";
        
        $stmt = $conn->query($sql);
        $resultado = $stmt->fetch();
        
        $receitas = floatval($resultado['receitas'] ?? 0);
        $despesas = floatval($resultado['despesas'] ?? 0);
        $saldo = $receitas - $despesas;
        
        return [
            'receitas' => $receitas,
            'despesas' => $despesas,
            'saldo' => $saldo
        ];
    } catch (PDOException $e) {
        error_log("Erro ao calcular totais: " . $e->getMessage());
        return [
            'receitas' => 0,
            'despesas' => 0,
            'saldo' => 0
        ];
    }
}
?>
