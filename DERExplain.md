Este diagrama representa o modelo de dados (DER - Diagrama Entidade-Relacionamento) de um **Sistema de Gestão Financeira Pessoal**. Ele mostra como o banco de dados está estruturado para permitir que usuários controlem gastos, criem orçamentos e definam metas de economia.

Aqui está uma explicação detalhada de cada parte do diagrama:

### 1. Entidade Central: USER (Usuário)
A tabela rosa (`USER`) é o coração do sistema. Quase todas as outras tabelas se conectam a ela.
*   **Função:** Armazena as informações das pessoas que utilizam o sistema.
*   **Dados:** ID (Chave Primária), nome, email, hash da senha (para segurança), tipo de usuário (userType) e data de criação.
*   **Relacionamentos:** Um usuário pode ter múltiplas transações, orçamentos, metas, contribuições e notificações.

### 2. Gestão de Gastos e Receitas
Estas duas tabelas controlam o fluxo diário de dinheiro:

*   **TRANSACTION (Transação - Laranja):**
    *   Registra cada movimentação financeira (compras, pagamentos, salários).
    *   Contém o valor (`amount`), data, descrição e método de pagamento.
    *   **Conexões:** Está ligada ao `USER` (quem gastou) e à `CATEGORY` (do que se trata o gasto).
*   **CATEGORY (Categoria - Azul Petróleo):**
    *   Serve para classificar as transações (ex: "Alimentação", "Transporte", "Salário").
    *   Contém o nome e o tipo (provavelmente receita ou despesa).
    *   **Conexões:** É criada por um `USER` e usada para classificar `TRANSACTION` e definir `BUDGET`.

### 3. Planejamento e Controle
*   **BUDGET (Orçamento - Azul Claro):**
    *   Define limites de gastos.
    *   **Lógica:** O usuário define um valor limite (`amountLimit`) para um mês e ano específicos, atrelado a uma categoria específica.
    *   **Exemplo:** O usuário define que pode gastar no máximo R$ 500,00 (amountLimit) em "Lazer" (categoryId) em Dezembro (month) de 2023 (year).

### 4. Metas de Poupança
Estas tabelas gerenciam objetivos de longo prazo (como comprar um carro ou fazer uma viagem):

*   **FINANCIAL_GOAL (Meta Financeira - Verde Claro):**
    *   Define o objetivo.
    *   Contém o valor alvo (`targetAmount`), o valor já economizado (`currentAmount`) e a data limite (`deadline`).
*   **CONTRIBUTION (Contribuição - Roxo):**
    *   Registra os depósitos feitos especificamente para uma meta.
    *   Diferente de uma transação comum, esta tabela liga o dinheiro diretamente a um `goalId` (ID da Meta).
    *   **Conexões:** Um usuário faz uma contribuição que financia ("funds") uma meta financeira.

### 5. Sistema de Avisos
*   **NOTIFICATION (Notificação - Vermelho):**
    *   Armazena alertas para o usuário (ex: "Você excedeu seu orçamento" ou "Meta atingida").
    *   Possui o status de leitura (`isRead`) e a mensagem.

### Resumo dos Relacionamentos (Cardinalidade)
A notação "pé de galinha" (as linhas que conectam as caixas) indica o seguinte padrão predominante: **Um para Muitos (1:N)**.

*   **1 Usuário** pode ter **Muitas** Transações.
*   **1 Categoria** pode ter **Muitas** Transações.
*   **1 Meta Financeira** pode receber **Muitas** Contribuições.
*   **1 Usuário** define **Muitos** Orçamentos.

Em resumo, este banco de dados permite que um aplicativo diga ao usuário: "Você gastou X em Alimentação este mês, o que é Y% do seu orçamento planejado, e você já economizou Z para sua viagem de férias."