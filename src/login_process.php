<?php
// src/login_process.php

// Inicia ou resume a sessão. Essencial para armazenar o estado de login do usuário
// e para passar mensagens de erro de volta para a página de login.
session_start();

// Inclui o arquivo de configuração e conexão com o banco de dados ($pdo).
require_once '../config/db.php';

// --- Verificação do Método da Requisição ---
// Garante que o script só processe dados enviados via método POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Coleta e Limpeza dos Dados do Formulário ---
    // Obtém username e password do array $_POST.
    // trim() remove espaços extras.
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // --- Validação Básica ---
    // Verifica se os campos não estão vazios.
    if (empty($username) || empty($password)) {
        // Se algum campo estiver vazio, define uma mensagem de erro na sessão.
        $_SESSION['error_message'] = "Usuário e senha são obrigatórios.";
        // Redireciona de volta para a página de login (index.php).
        header('Location: ../public/index.php');
        // Termina a execução do script.
        exit();
    }

    // --- Consulta ao Banco de Dados ---
    try {
        // Prepara a consulta SQL para buscar um usuário pelo nome de usuário fornecido.
        // Seleciona o id, username e a senha (hash) do usuário.
        // Usar prepared statements é fundamental para segurança (prevenção de SQL Injection).
        $sql = "SELECT id, username, password FROM users WHERE username = :username LIMIT 1";
        $stmt = $pdo->prepare($sql);

        // Associa o valor da variável $username ao placeholder :username na consulta SQL.
        // PDO::PARAM_STR indica que o valor é uma string.
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);

        // Executa a consulta preparada.
        $stmt->execute();

        // --- Verificação do Usuário e Senha ---
        // fetch() busca a próxima linha do resultado da consulta.
        // Como usamos LIMIT 1, ele buscará no máximo um usuário.
        // Se nenhum usuário for encontrado com esse username, $user será false.
        $user = $stmt->fetch();

        // Verifica se um usuário foi encontrado E se a senha fornecida corresponde ao hash armazenado.
        // password_verify() compara de forma segura a senha em texto plano ($password)
        // com o hash armazenado no banco ($user['password']).
        if ($user && password_verify($password, $user['password'])) {
            // --- Login Bem-Sucedido ---

            // Limpa sessões antigas e regenera o ID da sessão para prevenir Session Fixation.
            session_regenerate_id(true);

            // Armazena informações do usuário na sessão.
            // Guardar o ID é comum. Pode-se guardar o username também, se útil.
            // NÃO guarde a senha (nem o hash) na sessão.
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Redireciona o usuário para o painel (dashboard.php).
            header('Location: ../public/dashboard.php');
            exit();

        } else {
            // --- Login Falhou ---
            // Se o usuário não foi encontrado ou a senha está incorreta.
            // Define uma mensagem de erro genérica para não informar qual campo estava errado.
            $_SESSION['error_message'] = "Usuário ou senha inválidos.";
            // Redireciona de volta para a página de login.
            header('Location: ../public/index.php');
            exit();
        }

    } catch (PDOException $e) {
        // --- Erro no Banco de Dados ---
        // Se ocorrer uma exceção durante a interação com o banco.
        // Define uma mensagem de erro genérica. Em produção, logar $e->getMessage().
        $_SESSION['error_message'] = "Erro ao tentar fazer login. Tente novamente mais tarde.";
        // log_error($e->getMessage()); // Função hipotética para logar o erro real.
        // Redireciona de volta para a página de login.
        header('Location: ../public/index.php');
        exit();
    }

} else {
    // --- Método de Requisição Inválido ---
    // Se a requisição não foi POST, redireciona para a página de login.
    $_SESSION['error_message'] = "Método de requisição inválido.";
    header('Location: ../public/index.php');
    exit();
}
?>
