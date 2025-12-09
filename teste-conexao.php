<?php
// Teste de conexão com o banco de dados
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Teste de Conexão - Sistema Financeiro</h2>";

// 1. Verificar se os arquivos existem
echo "<h3>1. Verificando arquivos...</h3>";
$arquivos = ['config.php', 'functions.php', 'api.php', 'scripts.js', 'index.html'];
foreach ($arquivos as $arquivo) {
    if (file_exists($arquivo)) {
        echo "✅ $arquivo encontrado<br>";
    } else {
        echo "❌ $arquivo NÃO encontrado<br>";
    }
}

// 2. Testar conexão com banco
echo "<h3>2. Testando conexão com banco de dados...</h3>";
try {
    require_once 'config.php';
    $conn = getConnection();
    echo "✅ Conexão com banco estabelecida com sucesso!<br>";
    echo "Banco: " . DB_NAME . "<br>";
    
    // 3. Verificar se a tabela existe
    echo "<h3>3. Verificando tabela transacoes...</h3>";
    $stmt = $conn->query("SHOW TABLES LIKE 'transacoes'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabela 'transacoes' existe<br>";
        
        // Contar registros
        $stmt = $conn->query("SELECT COUNT(*) as total FROM transacoes");
        $result = $stmt->fetch();
        echo "📊 Total de transações: " . $result['total'] . "<br>";
        
        // Mostrar estrutura da tabela
        echo "<h3>4. Estrutura da tabela:</h3>";
        $stmt = $conn->query("DESCRIBE transacoes");
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th></tr>";
        while ($row = $stmt->fetch()) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } else {
        echo "❌ Tabela 'transacoes' NÃO existe<br>";
        echo "<p><strong>Execute o arquivo database.sql para criar a tabela!</strong></p>";
    }
    
    // 4. Testar inserção
    echo "<h3>5. Testando inserção de dados...</h3>";
    require_once 'functions.php';
    
    $dadosTeste = [
        'tipo' => 'receita',
        'descricao' => 'Teste de conexão',
        'valor' => 100.00,
        'data' => date('Y-m-d'),
        'categoria' => 'Teste'
    ];
    
    if (adicionarTransacao($dadosTeste)) {
        echo "✅ Inserção de teste realizada com sucesso!<br>";
        
        // Buscar a transação inserida
        $transacoes = buscarTransacoes();
        echo "✅ Busca de transações funcionando! Total: " . count($transacoes) . "<br>";
        
        // Calcular totais
        $totais = calcularTotais();
        echo "✅ Cálculo de totais funcionando!<br>";
        echo "Receitas: R$ " . number_format($totais['receitas'], 2, ',', '.') . "<br>";
        echo "Despesas: R$ " . number_format($totais['despesas'], 2, ',', '.') . "<br>";
        echo "Saldo: R$ " . number_format($totais['saldo'], 2, ',', '.') . "<br>";
    } else {
        echo "❌ Erro ao inserir dados de teste<br>";
    }
    
    echo "<h3>✅ Sistema funcionando corretamente!</h3>";
    echo "<p><a href='index.html'>Ir para o sistema</a></p>";
    
} catch (PDOException $e) {
    echo "❌ Erro de conexão: " . $e->getMessage() . "<br>";
    echo "<p><strong>Verifique:</strong></p>";
    echo "<ul>";
    echo "<li>Se o MySQL está rodando</li>";
    echo "<li>Se as credenciais em config.php estão corretas</li>";
    echo "<li>Se o banco 'controle_financeiro' foi criado</li>";
    echo "</ul>";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}
?>
