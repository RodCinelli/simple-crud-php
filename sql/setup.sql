-- Cria o banco de dados 'simple_crud_db' se ele ainda não existir.
-- Define o conjunto de caracteres para utf8mb4 e a collation para utf8mb4_unicode_ci
-- para suportar uma ampla gama de caracteres, incluindo emojis.
CREATE DATABASE IF NOT EXISTS simple_crud_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- Seleciona o banco de dados 'simple_crud_db' para usar nos comandos seguintes.
USE simple_crud_db;

-- Cria a tabela 'users' se ela ainda não existir (para login).
CREATE TABLE IF NOT EXISTS users (
    -- 'id': Chave primária auto-incrementável. Cada usuário terá um ID único.
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- 'username': Nome de usuário, não pode ser nulo e deve ser único.
    username VARCHAR(50) NOT NULL UNIQUE,
    -- 'password': Senha do usuário. VARCHAR(255) é recomendado para armazenar hashes de senha.
    password VARCHAR(255) NOT NULL,
    -- 'email': Email do usuário, não pode ser nulo e deve ser único.
    email VARCHAR(100) NOT NULL UNIQUE,
    -- 'created_at': Timestamp que registra automaticamente quando o usuário foi criado.
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cria a tabela 'employees' se ela ainda não existir (para o CRUD de funcionários).
CREATE TABLE IF NOT EXISTS employees (
    -- 'id': Chave primária auto-incrementável.
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- 'name': Nome completo do funcionário, não pode ser nulo.
    name VARCHAR(100) NOT NULL,
    -- 'position': Cargo do funcionário.
    position VARCHAR(100),
    -- 'department': Departamento do funcionário.
    department VARCHAR(100),
    -- 'hire_date': Data de contratação.
    hire_date DATE,
    -- 'salary': Salário, usando DECIMAL para precisão monetária (ex: 10 dígitos no total, 2 após a vírgula).
    salary DECIMAL(10, 2),
    -- 'created_at': Timestamp que registra automaticamente quando o registro do funcionário foi criado.
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

