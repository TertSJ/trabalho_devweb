// Sistema de Controle Financeiro - JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Definir data padrão como hoje
    const dataInput = document.getElementById('data');
    if (dataInput && !dataInput.value) {
        const hoje = new Date().toISOString().split('T')[0];
        dataInput.value = hoje;
    }

    // Carregar transações ao iniciar
    carregarTransacoes();

    // Submeter formulário
    const form = document.getElementById('form-transacao');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            adicionarTransacao();
        });
    }

    // Filtros
    const filtroBusca = document.getElementById('filtro-busca');
    const filtroTipo = document.getElementById('filtro-tipo');
    
    if (filtroBusca) {
        filtroBusca.addEventListener('input', filtrarTransacoes);
    }
    
    if (filtroTipo) {
        filtroTipo.addEventListener('change', filtrarTransacoes);
    }
});

// Carregar transações do banco
function carregarTransacoes() {
    fetch('api.php?action=listar')
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na resposta do servidor');
            }
            return response.json();
        })
        .then(data => {
            console.log('Dados recebidos:', data);
            if (data.success) {
                exibirTransacoes(data.transacoes);
                atualizarTotais(data.totais);
            } else {
                console.error('Erro:', data.message);
                mostrarAlerta(data.message || 'Erro ao carregar transações', 'warning');
            }
        })
        .catch(error => {
            console.error('Erro ao carregar transações:', error);
            mostrarAlerta('Erro ao conectar com o servidor. Verifique se o PHP está rodando.', 'danger');
        });
}

// Adicionar nova transação
function adicionarTransacao() {
    const tipo = document.getElementById('tipo').value;
    const descricao = document.getElementById('descricao').value;
    const valor = document.getElementById('valor').value;
    const data = document.getElementById('data').value;

    console.log('Tentando adicionar:', { tipo, descricao, valor, data });

    // Validação
    if (!tipo || !descricao || !valor || !data) {
        mostrarAlerta('Preencha todos os campos!', 'warning');
        return;
    }

    if (parseFloat(valor) <= 0) {
        mostrarAlerta('O valor deve ser maior que zero!', 'warning');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'adicionar');
    formData.append('tipo', tipo);
    formData.append('descricao', descricao);
    formData.append('valor', valor);
    formData.append('data', data);

    console.log('Enviando requisição para api.php...');

    fetch('api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Status da resposta:', response.status);
        if (!response.ok) {
            throw new Error('Erro HTTP: ' + response.status);
        }
        return response.text();
    })
    .then(text => {
        console.log('Resposta do servidor:', text);
        try {
            const data = JSON.parse(text);
            if (data.success) {
                mostrarAlerta('Transação adicionada com sucesso!', 'success');
                document.getElementById('form-transacao').reset();
                const hoje = new Date().toISOString().split('T')[0];
                document.getElementById('data').value = hoje;
                carregarTransacoes();
            } else {
                mostrarAlerta(data.message || 'Erro ao adicionar transação', 'danger');
            }
        } catch (e) {
            console.error('Erro ao parsear JSON:', e);
            console.error('Resposta recebida:', text);
            mostrarAlerta('Erro no servidor. Verifique o console (F12) para mais detalhes.', 'danger');
        }
    })
    .catch(error => {
        console.error('Erro na requisição:', error);
        mostrarAlerta('Erro ao conectar com o servidor: ' + error.message, 'danger');
    });
}

// Excluir transação
function excluirTransacao(id) {
    if (!confirm('Deseja realmente excluir esta transação?')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'excluir');
    formData.append('id', id);

    fetch('api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarAlerta('Transação excluída com sucesso!', 'success');
            carregarTransacoes();
        } else {
            mostrarAlerta(data.message || 'Erro ao excluir transação', 'danger');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        mostrarAlerta('Erro ao excluir transação', 'danger');
    });
}

// Exibir transações na tabela
function exibirTransacoes(transacoes) {
    const tbody = document.getElementById('lista-transacoes');
    
    if (!transacoes || transacoes.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-muted">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p>Nenhuma transação cadastrada</p>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = transacoes.map(t => `
        <tr>
            <td>${formatarData(t.data)}</td>
            <td>${t.descricao}</td>
            <td>
                <span class="badge bg-${t.tipo === 'receita' ? 'success' : 'danger'}">
                    <i class="fas fa-arrow-${t.tipo === 'receita' ? 'up' : 'down'}"></i>
                    ${t.tipo === 'receita' ? 'Receita' : 'Despesa'}
                </span>
            </td>
            <td class="text-${t.tipo === 'receita' ? 'success' : 'danger'} fw-bold">
                R$ ${formatarValor(t.valor)}
            </td>
            <td class="text-center">
                <button class="btn btn-sm btn-danger" onclick="excluirTransacao(${t.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

// Atualizar cards de totais
function atualizarTotais(totais) {
    document.getElementById('saldo').textContent = 'R$ ' + formatarValor(totais.saldo);
    document.getElementById('total-receitas').textContent = 'R$ ' + formatarValor(totais.receitas);
    document.getElementById('total-despesas').textContent = 'R$ ' + formatarValor(totais.despesas);
}

// Filtrar transações
function filtrarTransacoes() {
    const busca = document.getElementById('filtro-busca').value.toLowerCase();
    const tipo = document.getElementById('filtro-tipo').value;
    const linhas = document.querySelectorAll('#lista-transacoes tr');

    linhas.forEach(linha => {
        const descricao = linha.cells[1]?.textContent.toLowerCase() || '';
        const tipoTransacao = linha.querySelector('.badge')?.textContent.toLowerCase() || '';
        
        const matchBusca = descricao.includes(busca);
        const matchTipo = !tipo || tipoTransacao.includes(tipo);

        linha.style.display = (matchBusca && matchTipo) ? '' : 'none';
    });
}

// Formatar valor para exibição
function formatarValor(valor) {
    return parseFloat(valor).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// Formatar data para exibição
function formatarData(data) {
    const partes = data.split('-');
    return `${partes[2]}/${partes[1]}/${partes[0]}`;
}

// Mostrar alerta
function mostrarAlerta(mensagem, tipo) {
    const container = document.querySelector('.container');
    const alerta = document.createElement('div');
    alerta.className = `alert alert-${tipo} alert-dismissible fade show mt-3`;
    alerta.innerHTML = `
        ${mensagem}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    container.insertBefore(alerta, container.firstChild);
    
    setTimeout(() => {
        alerta.remove();
    }, 5000);
}
