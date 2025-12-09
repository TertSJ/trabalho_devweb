-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS controle_financeiro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE controle_financeiro;

-- Criar tabela de transações
CREATE TABLE IF NOT EXISTS transacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('receita', 'despesa') NOT NULL,
    valor DECIMAL(10, 2) NOT NULL,
    data DATE NOT NULL,
    categoria VARCHAR(100) NOT NULL,
    descricao TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tipo (tipo),
    INDEX idx_data (data),
    INDEX idx_categoria (categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir dados de exemplo (opcional)
INSERT INTO transacoes (tipo, valor, data, categoria, descricao) VALUES
('receita', 3500.00, '2024-12-01', 'Salário', 'Salário mensal'),
('despesa', 1200.00, '2024-12-05', 'Aluguel', 'Aluguel do apartamento'),
('despesa', 350.00, '2024-12-06', 'Alimentação', 'Compras do supermercado'),
('receita', 500.00, '2024-12-07', 'Freelance', 'Projeto web'),
('despesa', 150.00, '2024-12-08', 'Transporte', 'Combustível');
