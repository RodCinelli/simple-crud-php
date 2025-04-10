<?php
// public/dashboard.php

// Inicia ou resume a sessão para acessar as variáveis de sessão.
session_start();

// --- Verificação de Autenticação ---
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Você precisa fazer login para acessar esta página.";
    header('Location: index.php');
    exit();
}

// Recupera o nome de usuário da sessão para exibição.
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Usuário';

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CRUD Simples</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <div class="dashboard-header">
            <h2>Painel Principal</h2>
            <a href="../src/logout.php" class="logout-link">Sair</a>
        </div>

        <p>Bem-vindo(a) de volta, <strong><?php echo $username; ?></strong>!</p>
        <p>Esta é a sua área restrita.</p>

        <hr style="margin: 20px 0;">
        <div class="management-section">
            <h3>Gerenciar Funcionários</h3>
            <p>Clique no botão abaixo para adicionar, visualizar, editar ou excluir funcionários.</p>
            <a href="employees.php" class="button-link">Gerenciar Funcionários</a>
        </div>

    </div>
</body>

</html>