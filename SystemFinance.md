
# Sistema de Gerenciamento Financeiro Pessoal

## 1. Visão do Projeto

### Visão Geral
Criar um sistema simples, intuitivo e confiável que permita ao usuário organizar sua vida financeira, controlar gastos, planejar metas e visualizar sua saúde financeira de forma clara.

### Problema que o Sistema Resolve
- Falta de controle sobre despesas e receitas.
- Dificuldade de visualizar onde o dinheiro está sendo gasto.
- Ausência de ferramentas simples de planejamento.
- Sistemas financeiros complexos e pouco acessíveis.

### Objetivo Principal
- Registrar movimentações financeiras.
- Classificar transações por categoria.
- Gerar relatórios visuais.
- Acompanhar metas e orçamento.
- Auxiliar decisões financeiras.

### Valor Esperado
- Maior organização e clareza sobre o fluxo de dinheiro.
- Redução de gastos desnecessários.
- Melhor planejamento financeiro.

---

## 2. Escopo do Projeto

### Escopo IN (Funcionalidades Incluídas)
- Cadastro de usuários.
- Registro de entradas e saídas.
- Categorias personalizadas.
- Dashboard financeiro.
- Controle de orçamento.
- Metas financeiras.
- Relatórios por período.

### Escopo OUT (NÃO ESTARÁ INCLUIDO)
- Integração com bancos reais.
- IA para análise automática.
- Chat interno.O Diagrama Entidade–Relacionamento foi construído para representar a estrutura lógica dos dados envolvendo usuários, projetos, tarefas, comentários e roles de acesso.

1. Entidade: User

Representa os usuários cadastrados no sistema.

Atributos principais:

user_id (PK)

name

email

password_hash

role_id (FK)

created_at

Relacionamentos:

1:N com Project (um usuário cria vários projetos)

1:N com Task (um usuário pode ser responsável por várias tarefas)

1:N com Comment (um usuário registra vários comentários)

N:1 com Role (cada usuário pertence a um papel)

2. Entidade: Role

Define o tipo de permissão do usuário no sistema.

Atributos principais:

role_id (PK)

name

description

Relacionamentos:

1:N com User

3. Entidade: Project

Representa os projetos cadastrados no sistema.

Atributos principais:

project_id (PK)

name

description

owner_id (FK → User)

created_at

Relacionamentos:

N:1 com User

1:N com Task

4. Entidade: Task

Representa as tarefas pertencentes a um projeto.

Atributos principais:

task_id (PK)

project_id (FK → Project)

assigned_to (FK → User)

title

description

status

due_date

created_at

Relacionamentos:

N:1 com Project

N:1 com User (responsável)

1:N com Comment

5. Entidade: Comment

Representa comentários feitos em tarefas.

Atributos principais:

comment_id (PK)

task_id (FK → Task)

user_id (FK → User)

content

created_at

Relacionamentos:

N:1 com Task

N:1 com User

🔗 Resumo dos Relacionamentos
Entidade A	Relacionamento	Entidade B	Tipo
User	cria	Project	1:N
User	é responsável por	Task	1:N
User	registra	Comment	1:N
Role	possui	User	1:N
Project	possui	Task	1:N
Task	possui	Comment	1:N
- Sistema multiusuário empresarial.
- Pagamentos internos.
- Controle avançado de investimentos.

---

## 3. Público-Alvo

### Público Principal
- Adultos que desejam controlar suas finanças.
- Estudantes e trabalhadores.

### Público Secundário
- Pequenos empreendedores.
- Pessoas com objetivos financeiros específicos.

### Perfis
- Usuário básico.
- Usuário planejador.
- Usuário visual.

---

## 4. Regras de Negócio

### Cadastro e Autenticação
- Email único.
- Senha em padrão mínimo.

### Transações
- Campos obrigatórios: valor, data, tipo, categoria.
- Despesas diminuem saldo, receitas aumentam.
- Sem valores negativos.

### Orçamentos
- Associados a categorias.
- Alertas ao ultrapassar 90% e 100%.

### Metas
- Precisam ter valor total.
- Consideradas concluídas ao alcançar 100%.

### Relatórios
- Intervalo de datas.
- Agrupamento por categoria ou tipo.

---

## 5. Categorias do Sistema

### Categorias de Despesas
- Moradia
- Alimentação
- Transporte
- Saúde
- Educação
- Lazer
- Compras pessoais
- Contas e serviços
- Impostos
- Dívidas

### Categorias de Receitas
- Salário
- Trabalho extra
- Investimentos
- Reembolsos
- Outros

### Categorias para Planejamento
- Reserva de emergência
- Aposentadoria
- Viagem
- Compra de bens
- Fundo de estudos

---

## 6. Categorias Padrão Recomendadas

### Fixas
- Moradia
- Alimentação
- Transporte
- Saúde
- Educação
- Lazer
- Compras
- Contas
- Impostos
- Dívidas
- Salário
- Trabalho extra
- Investimentos

### Personalizáveis
- Café da manhã
- Meu pet
- Viagem específica
- Reforma da casa

---

# 7. Regras de Negócio

## Cadastro e Autenticação
- O usuário deve possuir um email único no sistema.
- A senha deve seguir critérios mínimos (8 caracteres, letras e números).
- O usuário só pode acessar seus próprios dados financeiros.

## Transações
- Cada transação deve ter: valor, data, categoria e tipo (entrada ou saída).
- Não é permitido registrar valores negativos.
- Despesas reduzem o saldo; receitas aumentam o saldo.
- Categorias padrão não podem ser apagadas, apenas categorias personalizadas.

## Orçamentos
- Um orçamento deve ser vinculado a uma categoria.
- O sistema deve alertar quando o uso do orçamento atingir 90%.
- Ao ultrapassar 100%, deve emitir alerta crítico.
- O orçamento deve ser mensal, reiniciando a cada ciclo.

## Metas Financeiras
- Uma meta deve conter valor objetivo total e prazo (opcional).
- A meta só pode ser concluída ao atingir 100% do valor.
- O usuário pode contribuir com depósitos associados à meta.

## Relatórios
- Relatórios devem ser gerados com base em intervalo de datas escolhido.
- Os valores devem ser agrupados por categoria ou tipo.
- O usuário pode exportar relatórios em diferentes formatos.

## Dashboard
- Exibir resumo financeiro (entradas, saídas e saldo).
- Exibir gráficos baseados em categorias e tendências.
- Deve ser atualizado automaticamente a cada nova transação.

---

# 8. Mapeamento de Tipos de Usuário para Requisitos

## Usuário Básico
- Necessita registrar despesas e receitas.
- Deve visualizar o saldo atual de forma simples.
- Deve acessar categorias básicas.

## Usuário Visual
- Deve ter acesso ao dashboard inicial ao logar.
- Precisa de gráficos organizados por categoria e por período.
- Deve visualizar tendências financeiras mensalmente.

## Usuário Planejador
- Deve definir metas financeiras e acompanhar o progresso.
- Deve configurar orçamentos mensais por categoria.
- Deve visualizar relatórios completos, detalhados e exportáveis.

---

# 9. Requisitos Funcionais (RF)

### **RF01 — Cadastro de Usuário**

O sistema deve permitir o cadastro de novos usuários com email e senha.

### **RF02 — Autenticação**

O sistema deve permitir que o usuário faça login utilizando suas credenciais.

### **RF03 — Registrar Transações**

O usuário deve poder registrar transações de entrada e saída com valor, data, tipo e categoria.

### **RF04 — Editar e Excluir Transações**

O sistema deve permitir editar ou remover transações individuais.

### **RF05 — Gerenciar Categorias**

O usuário deve visualizar categorias padrão e criar categorias personalizadas.

### **RF06 — Dashboard Resumido**

O sistema deve apresentar um painel com saldo, total de entradas e total de saídas.

### **RF07 — Relatórios Financeiros**

O usuário deve gerar relatórios filtrando por período e categoria.

### **RF08 — Gráficos Financeiros**

O sistema deve exibir gráficos de distribuição de despesas e evolução do saldo.

### **RF09 — Gerenciar Orçamentos**

O usuário deve criar orçamentos por categoria e acompanhar sua utilização.

### **RF10 — Sistema de Alertas**

O sistema deve notificar o usuário ao atingir 90% ou 100% do orçamento.

### **RF11 — Metas Financeiras**

O usuário deve definir metas financeiras com valor objetivo e acompanhar progresso.

### **RF12 — Exportação**

O sistema deve permitir exportar relatórios em PDF ou CSV (versão futura).

### **RF13 — Histórico**

O usuário deve visualizar todo o histórico de transações e metas concluídas.

---

# 10. Requisitos Não Funcionais (RNF)

### **RNF01 — Usabilidade**

A interface deve ser simples, intuitiva e responsiva, adequada para desktop e mobile.

### **RNF02 — Desempenho**

As páginas principais devem carregar em menos de 2 segundos.

### **RNF03 — Segurança**

* Senhas devem ser criptografadas.
* O usuário só pode acessar os próprios dados.
* A API deve utilizar HTTPS.

### **RNF04 — Confiabilidade**

O sistema deve manter integridade dos dados mesmo após erros inesperados.

### **RNF05 — Escalabilidade**

A arquitetura deve permitir aumento de usuários sem queda significativa de desempenho.

### **RNF06 — Disponibilidade**

O sistema deve estar disponível 99% do tempo.

### **RNF07 — Armazenamento**

Dados devem ser mantidos em banco de dados relacional ou não relacional (dependendo da arquitetura definida).

### **RNF08 — Manutenibilidade**

O código deve seguir boas práticas para facilitar manutenção e futuras expansões.

---

# 11. Casos de Uso (UML Simplificado)

### **UC01 — Cadastrar Usuário**

**Ator:** Usuário
**Fluxo Principal:**

1. Usuário preenche email e senha.
2. Sistema valida dados.
3. Sistema cria conta.
4. Exibe mensagem de sucesso.

### **UC02 — Fazer Login**

**Ator:** Usuário
**Fluxo Principal:**

1. Usuário insere credenciais.
2. Sistema valida login.
3. Usuário é direcionado ao dashboard.

### **UC03 — Registrar Transação**

**Ator:** Usuário
**Fluxo Principal:**

1. Usuário escolhe criar nova transação.
2. Preenche valor, categoria, tipo e data.
3. Sistema registra.
4. Dashboard é atualizado.

### **UC04 — Editar/Excluir Transação**

**Ator:** Usuário
**Fluxo Principal:**

1. Usuário seleciona transação.
2. Escolhe editar ou excluir.
3. Sistema atualiza informações.

### **UC05 — Gerenciar Categorias**

**Ator:** Usuário
**Fluxo Principal:**

1. Usuário visualiza categorias.
2. Pode criar categoria personalizada.
3. Sistema salva nova categoria.

### **UC06 — Criar Orçamento**

**Ator:** Usuário
**Fluxo Principal:**

1. Usuário seleciona a categoria.
2. Define valor do orçamento.
3. Sistema inicia monitoramento.

### **UC07 — Receber Alerta de Orçamento**

**Ator:** Sistema
**Fluxo Principal:**

1. Sistema monitora transações.
2. Ao atingir 90% ou 100% dispara alerta.

### **UC08 — Criar Meta Financeira**

**Ator:** Usuário
**Fluxo Principal:**

1. Usuário define nome, objetivo e valor final.
2. Sistema registra meta.
3. Usuário acompanha evolução.

### **UC09 — Gerar Relatório**

**Ator:** Usuário
**Fluxo Principal:**

1. Usuário seleciona período.
2. Sistema processa dados.
3. Exibe relatório e gráficos.

---


