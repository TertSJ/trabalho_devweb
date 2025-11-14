// Importa o banco de dados
import database from './database.js';

// Elementos do DOM
const form = document.getElementById('finance-form');
const tabelaTransacoes = document.getElementById('tabela-transacoes');
const saldoTotal = document.getElementById('saldo-total');
const receitasTotal = document.getElementById('receitas-total');
const despesasTotal = document.getElementById('despesas-total');

// Inicialização
document.addEventListener('DOMContentLoaded', async () => {
    try {
        // Aguarda o banco de dados estar pronto
        await database.initializeDB();
        
        // Carrega os dados iniciais
        await carregarDados();
        
        // Configura os listeners de atualização em tempo real
        configurarAtualizacoesEmTempoReal();
        
        // Configura os filtros
        configurarFiltros();
        
    } catch (error) {
        console.error('Erro ao inicializar a aplicação:', error);
        mostrarMensagem('Erro ao carregar os dados. Por favor, recarregue a página.', 'error');
    }
});

// Event Listeners
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    if (validarFormulario()) {
        const transacao = {
            tipo: document.getElementById('tipo').value,
            valor: parseFloat(document.getElementById('valor').value).toFixed(2),
            data: document.getElementById('data').value,
            categoria: document.getElementById('categoria').value.trim(),
            descricao: document.getElementById('descricao').value.trim()
        };
        
        try {
            await database.adicionarTransacao(transacao);
            form.reset();
            mostrarMensagem('Transação adicionada com sucesso!', 'success');
        } catch (error) {
            console.error('Erro ao adicionar transação:', error);
            mostrarMensagem('Erro ao adicionar transação. Tente novamente.', 'error');
        }
    }
});

// Configura os listeners para atualizações em tempo real
function configurarAtualizacoesEmTempoReal() {
    database.addEventListener('dados-atualizados', async (event) => {
        const { tipo, dados } = event.detail;
        
        // Atualiza a interface com base no tipo de mudança
        switch (tipo) {
            case 'transacao-adicionada':
            case 'transacao-removida':
                await carregarDados();
                break;
        }
    });
}

// Carrega os dados do banco e atualiza a interface
async function carregarDados() {
    try {
        // Mostra um indicador de carregamento
        tabelaTransacoes.querySelector('tbody').innerHTML = '<tr><td colspan="5" class="text-center">Carregando...</td></tr>';
        
        // Obtém as transações do banco de dados
        const transacoes = await database.obterTodasTransacoes();
        
        // Ordena por data (mais recente primeiro)
        transacoes.sort((a, b) => new Date(b.data) - new Date(a.data));
        
        // Atualiza a tabela e o resumo
        atualizarTabela(transacoes);
        await atualizarResumo();
        
    } catch (error) {
        console.error('Erro ao carregar dados:', error);
        throw error;
    }
}

// Configura os filtros
function configurarFiltros() {
    // Adiciona event listeners para os filtros
    document.getElementById('filtro-data').addEventListener('change', async (e) => {
        await aplicarFiltros();
    });
    
    document.getElementById('filtro-tipo').addEventListener('change', async (e) => {
        await aplicarFiltros();
    });
    
    document.getElementById('filtro-categoria').addEventListener('change', async (e) => {
        await aplicarFiltros();
    });
}

// Aplica os filtros selecionados
async function aplicarFiltros() {
    try {
        const dataSelecionada = document.getElementById('filtro-data').value;
        const tipoSelecionado = document.getElementById('filtro-tipo').value;
        const categoriaSelecionada = document.getElementById('filtro-categoria').value;
        
        let transacoes = await database.obterTodasTransacoes();
        
        // Aplica os filtros
        if (dataSelecionada) {
            transacoes = transacoes.filter(t => t.data === dataSelecionada);
        }
        
        if (tipoSelecionado) {
            transacoes = transacoes.filter(t => t.tipo === tipoSelecionado);
        }
        
        if (categoriaSelecionada) {
            transacoes = transacoes.filter(t => t.categoria === categoriaSelecionada);
        }
        
        // Atualiza a tabela com os resultados filtrados
        atualizarTabela(transacoes);
        
    } catch (error) {
        console.error('Erro ao aplicar filtros:', error);
        mostrarMensagem('Erro ao filtrar transações.', 'error');
    }
}

// Mostra uma mensagem para o usuário
function mostrarMensagem(mensagem, tipo = 'info') {
    const container = document.getElementById('mensagens');
    const mensagemElement = document.createElement('div');
    mensagemElement.className = `alert alert-${tipo}`;
    mensagemElement.textContent = mensagem;
    
    container.appendChild(mensagemElement);
    
    // Remove a mensagem após 5 segundos
    setTimeout(() => {
        mensagemElement.remove();
    }, 5000);
}

// Funções de validação
function validarFormulario() {
    let valido = true;
    const campos = ['tipo', 'valor', 'data', 'categoria'];
    
    campos.forEach(campo => {
        const elemento = document.getElementById(campo);
        const erro = document.getElementById(`${campo}-error`);
        
        if (campo === 'valor' && (isNaN(elemento.value) || parseFloat(elemento.value) <= 0)) {
            mostrarErro(campo, 'Informe um valor maior que zero.');
            valido = false;
        } else if (campo === 'categoria' && elemento.value.trim() === '') {
            mostrarErro(campo, 'Categoria é obrigatória.');
            valido = false;
        } else if (elemento.value === '' && elemento.required) {
            mostrarErro(campo, 'Campo obrigatório.');
            valido = false;
        } else {
            esconderErro(campo);
        }
    });
    
    return valido;
}

function mostrarErro(campo, mensagem) {
    const erro = document.getElementById(`${campo}-error`);
    erro.textContent = mensagem;
    erro.style.display = 'block';
}

function esconderErro(campo) {
    const erro = document.getElementById(`${campo}-error`);
    erro.style.display = 'none';
}

// Funções CRUD
function adicionarTransacao(transacao) {
    transacoes.push(transacao);
    salvarNoLocalStorage();
    atualizarTabela();
    atualizarResumo();
}

function removerTransacao(id) {
    if (confirm('Tem certeza que deseja remover esta transação?')) {
        transacoes = transacoes.filter(transacao => transacao.id !== id);
        salvarNoLocalStorage();
        atualizarTabela();
        atualizarResumo();
    }
}

// Funções de atualização da UI
function atualizarTabela() {
    const tbody = tabelaTransacoes.querySelector('tbody');
    tbody.innerHTML = '';
    
    if (transacoes.length === 0) {
        const tr = document.createElement('tr');
        tr.innerHTML = '<td colspan="6" class="text-center">Nenhuma transação cadastrada</td>';
        tbody.appendChild(tr);
        return;
    }
    
    transacoes.forEach(transacao => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${formatarData(transacao.data)}</td>
            <td>${transacao.categoria}</td>
            <td>${transacao.descricao || '-'}</td>
            <td class="${transacao.tipo === 'receita' ? 'text-success' : 'text-danger'}">
                ${transacao.tipo === 'receita' ? '+' : '-'} R$ ${parseFloat(transacao.valor).toFixed(2)}
            </td>
            <td>
                <button onclick="removerTransacao(${transacao.id})" class="btn-excluir">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function atualizarResumo() {
    const receitas = transacoes
        .filter(t => t.tipo === 'receita')
        .reduce((total, t) => total + parseFloat(t.valor), 0);
        
    const despesas = transacoes
        .filter(t => t.tipo === 'despesa')
        .reduce((total, t) => total + parseFloat(t.valor), 0);
        
    const saldo = receitas - despesas;
    
    receitasTotal.textContent = `R$ ${receitas.toFixed(2)}`;
    despesasTotal.textContent = `R$ ${despesas.toFixed(2)}`;
    saldoTotal.textContent = `R$ ${saldo.toFixed(2)}`;
    
    // Atualizar cor do saldo
    saldoTotal.className = saldo >= 0 ? 'text-success' : 'text-danger';
}

// Funções auxiliares
function formatarData(dataString) {
    const data = new Date(dataString);
    return data.toLocaleDateString('pt-BR');
}

function salvarNoLocalStorage() {
    localStorage.setItem('transacoes', JSON.stringify(transacoes));
}