<?php

include 'conexao.php';

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['id'];
$mensagem_sucesso = '';
$mensagem_erro = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        // Adicionar transação
        if ($action === 'adicionar') {
            $tipo = $conexao->real_escape_string($_POST['tipo']);
            $valor = floatval($_POST['valor']);
            $data = $conexao->real_escape_string($_POST['data']);
            $categoria = $conexao->real_escape_string($_POST['categoria']);
            $descricao = isset($_POST['descricao']) ? $conexao->real_escape_string($_POST['descricao']) : '';
            
            $sql_insert = "INSERT INTO transacoes (usuario_id, tipo, valor, data, categoria, descricao) 
                           VALUES ($usuario_id, '$tipo', $valor, '$data', '$categoria', '$descricao')";
            
            if ($conexao->query($sql_insert)) {
                $mensagem_sucesso = "Transação adicionada com sucesso!";
            } else {
                $mensagem_erro = "Erro ao adicionar transação: " . $conexao->error;
            }
        }
        
        // Excluir transação
        if ($action === 'excluir' && isset($_POST['id'])) {
            $id = intval($_POST['id']);
            $sql_delete = "DELETE FROM transacoes WHERE id = $id AND usuario_id = $usuario_id";
            
            if ($conexao->query($sql_delete)) {
                $mensagem_sucesso = "Transação excluída com sucesso!";
            } else {
                $mensagem_erro = "Erro ao excluir transação.";
            }
        }
    }
}
$hoje = date('Y-m-d');
echo "Hoje é $hoje";
$mes = date('m');
$ano = date('Y');
$sql_transacoes = "SELECT * FROM transacoes 
                   WHERE usuario_id = $usuario_id
                   ORDER BY data DESC, id DESC";
$result_transacoes = $conexao->query($sql_transacoes);
$transacoes = [];
if ($result_transacoes) {
    while ($row = $result_transacoes->fetch_assoc()) {
        $transacoes[] = $row;
    }
}

// Calcular totais

$sql_totais_mes = "SELECT 
                    SUM(CASE WHEN tipo = 'receita' THEN valor ELSE 0 END) as receitas,
                    SUM(CASE WHEN tipo = 'despesa' THEN valor ELSE 0 END) as despesas
                   FROM transacoes 
                   WHERE usuario_id = $usuario_id 
                   AND MONTH(data) = $mes 
                   AND YEAR(data) = $ano";
$result_totais_mes = $conexao->query($sql_totais_mes);
$totais_mes_row = $result_totais_mes->fetch_assoc();
$totais_mes = [
    'receitas' => $totais_mes_row['receitas'] ?? 0,
    'despesas' => $totais_mes_row['despesas'] ?? 0,
    'saldo' => ($totais_mes_row['receitas'] ?? 0) - ($totais_mes_row['despesas'] ?? 0)
];


$sql_totais = "SELECT 
                SUM(CASE WHEN tipo = 'receita' THEN valor ELSE 0 END) as receitas,
                SUM(CASE WHEN tipo = 'despesa' THEN valor ELSE 0 END) as despesas
               FROM transacoes WHERE usuario_id = $usuario_id";
$result_totais = $conexao->query($sql_totais);
$totais_row = $result_totais->fetch_assoc();
$totais = [
    'receitas' => $totais_row['receitas'] ?? 0,
    'despesas' => $totais_row['despesas'] ?? 0,
    'saldo' => ($totais_row['receitas'] ?? 0) - ($totais_row['despesas'] ?? 0)
];
?>
