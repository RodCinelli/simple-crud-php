<?php
// public/index.php

// Inicia ou resume uma sessão existente.
// É necessário para verificar se o usuário já está logado
// e para exibir mensagens de erro/sucesso vindas de outros scripts.
session_start();

// Verifica se o usuário JÁ está logado (se a variável de sessão 'user_id' existe).
// Se sim, redireciona diretamente para o dashboard para evitar que ele veja a tela de login novamente.
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit(); // Termina a execução do script após o redirecionamento.
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CRUD Simples</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <h2>Login</h2>

        <?php
        // Verifica se existe uma mensagem de erro na sessão (enviada pelo login_process.php).
        if (isset($_SESSION['error_message'])) {
            // Exibe a mensagem de erro dentro de uma div com classe CSS apropriada.
            echo '<div class="error-message">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
            // Remove a mensagem da sessão para que ela não seja exibida novamente.
            unset($_SESSION['error_message']);
        }

        // Verifica se existe uma mensagem de sucesso na sessão (enviada pelo register_process.php).
        if (isset($_SESSION['success_message'])) {
            // Exibe a mensagem de sucesso.
            echo '<div class="success-message">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
            // Remove a mensagem da sessão.
            unset($_SESSION['success_message']);
        }
        ?>

        <form action="../src/login_process.php" method="POST">
            <div class="form-group">
                <label for="username">Usuário:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Entrar</button>
        </form>

        <a href="register.php" class="link">Não tem uma conta? Cadastre-se</a>
    </div>

</body>

</html>