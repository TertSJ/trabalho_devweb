<?php
// Habilitar exibição de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 0); // Não mostrar erros na saída
ini_set('log_errors', 1);

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    // Verificar se os arquivos existem
    if (!file_exists('config.php')) {
        throw new Exception('Arquivo config.php não encontrado');
    }
    if (!file_exists('functions.php')) {
        throw new Exception('Arquivo functions.php não encontrado');
    }
    
    require_once 'config.php';
    require_once 'functions.php';
    
    // Listar transações
    if (isset($_GET['action']) && $_GET['action'] === 'listar') {
        $transacoes = buscarTransacoes();
        $totais = calcularTotais();
        
        $response = [
            'success' => true,
            'transacoes' => $transacoes,
            'totais' => $totais
        ];
    }
    
    // Adicionar transação
    elseif (isset($_POST['action']) && $_POST['action'] === 'adicionar') {
        // Log dos dados recebidos
        error_log('POST recebido: ' . print_r($_POST, true));
        
        if (empty($_POST['tipo']) || empty($_POST['descricao']) || empty($_POST['valor']) || empty($_POST['data'])) {
            $response['message'] = 'Todos os campos são obrigatórios';
            $response['debug'] = [
                'tipo' => isset($_POST['tipo']) ? $_POST['tipo'] : 'vazio',
                'descricao' => isset($_POST['descricao']) ? $_POST['descricao'] : 'vazio',
                'valor' => isset($_POST['valor']) ? $_POST['valor'] : 'vazio',
                'data' => isset($_POST['data']) ? $_POST['data'] : 'vazio'
            ];
        } else {
            $dados = [
                'tipo' => $_POST['tipo'],
                'descricao' => $_POST['descricao'],
                'valor' => $_POST['valor'],
                'data' => $_POST['data'],
                'categoria' => $_POST['descricao'] // Usando descrição como categoria
            ];
            
            $resultado = adicionarTransacao($dados);
            if ($resultado) {
                $response['success'] = true;
                $response['message'] = 'Transação adicionada com sucesso';
            } else {
                $response['message'] = 'Erro ao adicionar transação no banco de dados';
            }
        }
    }
    
    // Excluir transação
    elseif (isset($_POST['action']) && $_POST['action'] === 'excluir') {
        if (empty($_POST['id'])) {
            $response['message'] = 'ID da transação não informado';
        } else {
            if (excluirTransacao($_POST['id'])) {
                $response['success'] = true;
                $response['message'] = 'Transação excluída com sucesso';
            } else {
                $response['message'] = 'Erro ao excluir transação';
            }
        }
    }
    
    else {
        $response['message'] = 'Ação inválida ou não especificada';
        $response['debug'] = [
            'GET' => $_GET,
            'POST' => $_POST
        ];
    }
    
} catch (PDOException $e) {
    $response['message'] = 'Erro no banco de dados: ' . $e->getMessage();
    error_log('Erro PDO: ' . $e->getMessage());
} catch (Exception $e) {
    $response['message'] = 'Erro no servidor: ' . $e->getMessage();
    error_log('Erro: ' . $e->getMessage());
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>
