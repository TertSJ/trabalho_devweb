class Database {
    constructor() {
        this.dbName = 'controle-financeiro';
        this.dbVersion = 1;
        this.db = null;
        this.broadcastChannel = new BroadcastChannel('finance-sync');
        this.setupBroadcastChannel();
        this.initializeDB();
    }

    async initializeDB() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName, this.dbVersion);

            request.onerror = (event) => {
                console.error('Erro ao abrir o banco de dados:', event.target.error);
                reject(event.target.error);
            };

            request.onsuccess = (event) => {
                this.db = event.target.result;
                console.log('Banco de dados aberto com sucesso');
                resolve(this.db);
            };

            request.onupgradeneeded = (event) => {
                const db = event.target.result;
                
                // Cria a loja de transações se não existir
                if (!db.objectStoreNames.contains('transacoes')) {
                    const store = db.createObjectStore('transacoes', { keyPath: 'id', autoIncrement: true });
                    
                    // Cria índices para buscas rápidas
                    store.createIndex('tipo', 'tipo', { unique: false });
                    store.createIndex('data', 'data', { unique: false });
                    store.createIndex('categoria', 'categoria', { unique: false });
                    
                    console.log('Estrutura do banco de dados criada');
                }
            };
        });
    }

    // Métodos CRUD
    async adicionarTransacao(transacao) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['transacoes'], 'readwrite');
            const store = transaction.objectStore('transacoes');
            
            // Adiciona timestamp se não existir
            if (!transacao.dataCriacao) {
                transacao.dataCriacao = new Date().toISOString();
            }
            
            const request = store.add(transacao);
            
            request.onsuccess = () => {
                this.notificarMudancas('transacao-adicionada', { id: request.result, ...transacao });
                resolve(request.result);
            };
            
            request.onerror = (event) => {
                console.error('Erro ao adicionar transação:', event.target.error);
                reject(event.target.error);
            };
        });
    }

    async obterTodasTransacoes() {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['transacoes'], 'readonly');
            const store = transaction.objectStore('transacoes');
            const request = store.getAll();
            
            request.onsuccess = () => resolve(request.result || []);
            request.onerror = (event) => {
                console.error('Erro ao buscar transações:', event.target.error);
                reject(event.target.error);
            };
        });
    }

    async removerTransacao(id) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['transacoes'], 'readwrite');
            const store = transaction.objectStore('transacoes');
            
            // Primeiro obtém a transação para notificar após a remoção
            const getRequest = store.get(id);
            
            getRequest.onsuccess = () => {
                const transacao = getRequest.result;
                if (!transacao) {
                    reject(new Error('Transação não encontrada'));
                    return;
                }
                
                const deleteRequest = store.delete(id);
                
                deleteRequest.onsuccess = () => {
                    this.notificarMudancas('transacao-removida', { id, ...transacao });
                    resolve(true);
                };
                
                deleteRequest.onerror = (event) => {
                    console.error('Erro ao remover transação:', event.target.error);
                    reject(event.target.error);
                };
            };
            
            getRequest.onerror = (event) => {
                console.error('Erro ao buscar transação para remoção:', event.target.error);
                reject(event.target.error);
            };
        });
    }

    // Métodos de relatório
    async obterResumo() {
        const transacoes = await this.obterTodasTransacoes();
        
        const receitas = transacoes
            .filter(t => t.tipo === 'receita')
            .reduce((total, t) => total + parseFloat(t.valor), 0);
            
        const despesas = transacoes
            .filter(t => t.tipo === 'despesa')
            .reduce((total, t) => total + parseFloat(t.valor), 0);
            
        return {
            receitas: receitas.toFixed(2),
            despesas: despesas.toFixed(2),
            saldo: (receitas - despesas).toFixed(2)
        };
    }

    // Sincronização entre abas/janelas
    setupBroadcastChannel() {
        this.broadcastChannel.onmessage = (event) => {
            const { tipo, dados } = event.data;
            
            switch (tipo) {
                case 'transacao-adicionada':
                case 'transacao-atualizada':
                case 'transacao-removida':
                    this.dispatchEvent(new CustomEvent('dados-atualizados', { detail: { tipo, dados } }));
                    break;
            }
        };
    }

    notificarMudancas(tipo, dados) {
        this.broadcastChannel.postMessage({ tipo, dados });
    }

    // Adiciona suporte a EventTarget
    addEventListener(event, callback) {
        if (!this._listeners) this._listeners = {};
        if (!this._listeners[event]) this._listeners[event] = [];
        this._listeners[event].push(callback);
    }

    removeEventListener(event, callback) {
        if (!this._listeners || !this._listeners[event]) return;
        this._listeners[event] = this._listeners[event].filter(cb => cb !== callback);
    }

    dispatchEvent(event) {
        if (!this._listeners || !this._listeners[event.type]) return;
        this._listeners[event.type].forEach(callback => callback(event));
    }
}

// Exporta uma instância única do banco de dados
const database = new Database();
export default database;
