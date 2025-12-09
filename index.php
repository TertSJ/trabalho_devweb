<?php
require_once 'config.php';
require_once 'functions.php';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'adicionar':
                adicionarTransacao($_POST);
                break;
            case 'excluir':
                excluirTransacao($_POST['id']);
                break;
        }
    }
}

// Buscar dados
$transacoes = buscarTransacoes();
$totais = calcularTotais();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Controle Financeiro Pessoal</title>
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.php">
        <i class="fas fa-wallet me-2"></i>Controle Financeiro
      </a>
    </div>
  </nav>

  <div class="container mt-4">
    
    <!-- Cards de Resumo -->
    <div class="row mb-4">
      <div class="col-md-4">
        <div class="card text-white bg-success">
          <div class="card-body">
            <h5 class="card-title"><i class="fas fa-arrow-up me-2"></i>Receitas</h5>
            <h3 class="card-text">R$ <?php echo number_format($totais['receitas'], 2, ',', '.'); ?></h3>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-white bg-danger">
          <div class="card-body">
            <h5 class="card-title"><i class="fas fa-arrow-down me-2"></i>Despesas</h5>
            <h3 class="card-text">R$ <?php echo number_format($totais['despesas'], 2, ',', '.'); ?></h3>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-white <?php echo $totais['saldo'] >= 0 ? 'bg-info' : 'bg-warning'; ?>">
          <div class="card-body">
            <h5 class="card-title"><i class="fas fa-balance-scale me-2"></i>Saldo</h5>
            <h3 class="card-text">R$ <?php echo number_format($totais['saldo'], 2, ',', '.'); ?></h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Formulário de Cadastro -->
    <div class="card mb-4">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Nova Transação</h5>
      </div>
      <div class="card-body">
        <form method="POST" id="finance-form">
          <input type="hidden" name="action" value="adicionar">
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="tipo" class="form-label">Tipo *</label>
              <select class="form-select" id="tipo" name="tipo" required>
                <option value="">Selecione</option>
                <option value="receita">Receita</option>
                <option value="despesa">Despesa</option>
              </select>
            </div>
            
            <div class="col-md-6 mb-3">
              <label for="valor" class="form-label">Valor (R$) *</label>
              <input type="number" class="form-control" id="valor" name="valor" step="0.01" min="0.01" placeholder="Ex: 150.00" required>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="data" class="form-label">Data *</label>
              <input type="date" class="form-control" id="data" name="data" required>
            </div>
            
            <div class="col-md-6 mb-3">
              <label for="categoria" class="form-label">Categoria *</label>
              <input type="text" class="form-control" id="categoria" name="categoria" placeholder="Ex: Alimentação" required>
            </div>
          </div>

          <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" id="descricao" name="descricao" rows="2" placeholder="Descrição opcional"></textarea>
          </div>

          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Adicionar Transação
          </button>
        </form>
      </div>
    </div>

    <!-- Lista de Transações -->
    <div class="card">
      <div class="card-header bg-secondary text-white">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Histórico de Transações</h5>
      </div>
      <div class="card-body">
        <?php if (empty($transacoes)): ?>
          <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>Nenhuma transação cadastrada ainda.
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>Data</th>
                  <th>Tipo</th>
                  <th>Categoria</th>
                  <th>Descrição</th>
                  <th>Valor</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($transacoes as $transacao): ?>
                <tr>
                  <td><?php echo date('d/m/Y', strtotime($transacao['data'])); ?></td>
                  <td>
                    <?php if ($transacao['tipo'] === 'receita'): ?>
                      <span class="badge bg-success"><i class="fas fa-arrow-up"></i> Receita</span>
                    <?php else: ?>
                      <span class="badge bg-danger"><i class="fas fa-arrow-down"></i> Despesa</span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo htmlspecialchars($transacao['categoria']); ?></td>
                  <td><?php echo htmlspecialchars($transacao['descricao']); ?></td>
                  <td class="<?php echo $transacao['tipo'] === 'receita' ? 'text-success' : 'text-danger'; ?>">
                    R$ <?php echo number_format($transacao['valor'], 2, ',', '.'); ?>
                  </td>
                  <td>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Deseja realmente excluir esta transação?');">
                      <input type="hidden" name="action" value="excluir">
                      <input type="hidden" name="id" value="<?php echo $transacao['id']; ?>">
                      <button type="submit" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>

  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="scripts.js"></script>
</body>
</html>
