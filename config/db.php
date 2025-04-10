<?php
// config/db.php

// --- Configurações do Banco de Dados ---
// Defina aqui as credenciais e detalhes de conexão com o seu banco de dados MySQL local.

// Host do banco de dados (geralmente 'localhost' ou '127.0.0.1' para desenvolvimento local)
define('DB_HOST', 'localhost');

// Nome de usuário do banco de dados
define('DB_USER', 'root');

// Senha do banco de dados
define('DB_PASS', ''); // Senha padrão do XAMPP/WAMP é vazia, no MAMP é 'root'

// Nome do banco de dados que criamos no setup.sql
define('DB_NAME', 'simple_crud_db');

// DSN (Data Source Name) - String de conexão para o PDO
// Especifica o tipo de banco (mysql), o host e o nome do banco.
// charset=utf8mb4 garante a codificação correta para suportar diversos caracteres.
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";

// --- Opções do PDO ---
// Configurações adicionais para a conexão PDO.
$options = [
    // PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION:
    // Faz o PDO lançar exceções em caso de erros de SQL,
    // o que facilita o tratamento de erros com blocos try-catch.
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,

    // PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC:
    // Define o modo padrão de busca de resultados como um array associativo
    // (nomes das colunas como chaves), tornando o acesso aos dados mais intuitivo.
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

    // PDO::ATTR_EMULATE_PREPARES => false:
    // Desativa a emulação de prepared statements nativa do PDO,
    // forçando o uso dos prepared statements reais do driver do banco de dados.
    // Isso geralmente aumenta a segurança contra SQL injection.
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// --- Tentativa de Conexão ---
// Usa um bloco try-catch para tentar estabelecer a conexão.
// Se a conexão falhar, uma PDOException será lançada.
try {
    // Cria uma nova instância da classe PDO para conectar ao banco de dados.
    // Passa o DSN, usuário, senha e as opções configuradas.
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

    // Se chegou até aqui, a conexão foi bem-sucedida!
    // A variável $pdo agora contém o objeto de conexão e pode ser usada
    // em outros scripts para interagir com o banco de dados.
} catch (PDOException $e) {
    // Se uma PDOException for capturada (erro na conexão):
    // Exibe uma mensagem de erro genérica para o usuário final
    // e termina a execução do script para evitar expor detalhes sensíveis.
    // Em um ambiente de produção, você registraria o erro detalhado ($e->getMessage())
    // em um arquivo de log em vez de exibi-lo diretamente.
    die("Erro na conexão com o banco de dados: " . $e->getMessage()); // Em produção, use uma mensagem genérica e logue o erro.
}

// Nota: Este script apenas estabelece a conexão.
// Outros scripts PHP (como login_process.php, register_process.php)
// precisarão incluir este arquivo (`require_once '../config/db.php';`)
// para ter acesso à variável $pdo e poder executar queries.
?>