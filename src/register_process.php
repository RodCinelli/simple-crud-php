<?php
// src/register_process.php

// Inicia ou resume a sessão para poder armazenar mensagens de erro/sucesso.
session_start();

// Inclui o arquivo de configuração e conexão com o banco de dados.
// require_once garante que o arquivo seja incluído apenas uma vez.
// O caminho '../config/db.php' sobe um nível de diretório (de src para o raiz)
// e depois entra em config para encontrar db.php.
require_once '../config/db.php'; // $pdo estará disponível aqui

// --- Verificação do Método da Requisição ---
// Verifica se a requisição HTTP foi feita usando o método POST.
// Formulários de cadastro/login devem usar POST por segurança e semântica.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Coleta e Limpeza dos Dados do Formulário ---
    // Obtém os dados enviados pelo formulário através do array superglobal $_POST.
    // trim() remove espaços em branco do início e do fim das strings.
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // --- Validações Básicas ---
    $errors = []; // Array para armazenar mensagens de erro de validação.

    // Verifica se algum campo obrigatório está vazio.
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "Todos os campos são obrigatórios.";
    }

    // Valida o formato do e-mail usando filter_var.
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Formato de e-mail inválido.";
    }

    // Verifica se as senhas digitadas coincidem.
    if ($password !== $confirm_password) {
        $errors[] = "As senhas não coincidem.";
    }

    // Verifica o comprimento mínimo da senha (exemplo: 6 caracteres).
    if (strlen($password) < 6) {
        $errors[] = "A senha deve ter pelo menos 6 caracteres.";
    }

    // --- Verifica se Usuário ou Email Já Existem (Consulta ao Banco) ---
    // Só executa estas verificações se não houver erros de validação básicos.
    if (empty($errors)) {
        try {
            // Prepara uma consulta SQL para verificar se o username já existe.
            // Usar prepared statements (com placeholders como :username) é CRUCIAL para prevenir SQL Injection.
            $sql_check_user = "SELECT id FROM users WHERE username = :username LIMIT 1";
            $stmt_check_user = $pdo->prepare($sql_check_user);
            // Associa o valor da variável $username ao placeholder :username.
            $stmt_check_user->bindParam(':username', $username, PDO::PARAM_STR);
            // Executa a consulta preparada.
            $stmt_check_user->execute();
            // Verifica se a consulta retornou alguma linha (rowCount > 0 significa que o usuário já existe).
            if ($stmt_check_user->rowCount() > 0) {
                $errors[] = "Este nome de usuário já está em uso.";
            }

            // Prepara uma consulta SQL para verificar se o email já existe.
            $sql_check_email = "SELECT id FROM users WHERE email = :email LIMIT 1";
            $stmt_check_email = $pdo->prepare($sql_check_email);
            // Associa o valor da variável $email ao placeholder :email.
            $stmt_check_email->bindParam(':email', $email, PDO::PARAM_STR);
            // Executa a consulta.
            $stmt_check_email->execute();
            // Verifica se o email já existe.
            if ($stmt_check_email->rowCount() > 0) {
                $errors[] = "Este endereço de e-mail já está cadastrado.";
            }

        } catch (PDOException $e) {
            // Se ocorrer um erro durante a consulta ao banco (ex: problema de conexão),
            // adiciona uma mensagem de erro genérica. Em produção, logar $e->getMessage().
            $errors[] = "Erro ao verificar dados no banco de dados. Tente novamente.";
            // log_error($e->getMessage()); // Função hipotética para logar o erro real.
        }
    }

    // --- Processamento Final ---
    // Verifica se o array $errors está vazio (nenhum erro encontrado).
    if (empty($errors)) {
        // Se não houver erros, pode prosseguir com o cadastro.

        // --- Hashing da Senha ---
        // É FUNDAMENTAL hashear a senha antes de armazená-la no banco.
        // password_hash() cria um hash seguro e moderno da senha.
        // PASSWORD_DEFAULT é recomendado pois usa o algoritmo mais forte disponível no PHP
        // e pode ser atualizado automaticamente em versões futuras do PHP.
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // --- Inserção no Banco de Dados ---
        try {
            // Prepara a instrução SQL INSERT para adicionar o novo usuário.
            // Usa placeholders (:username, :email, :password) para segurança.
            $sql_insert = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
            $stmt_insert = $pdo->prepare($sql_insert);

            // Associa os valores das variáveis PHP aos placeholders na consulta SQL.
            // Especifica o tipo de dado (PDO::PARAM_STR) para maior clareza e segurança.
            $stmt_insert->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt_insert->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt_insert->bindParam(':password', $hashed_password, PDO::PARAM_STR); // Armazena o hash!

            // Executa a instrução INSERT preparada.
            $stmt_insert->execute();

            // --- Sucesso ---
            // Se a execução chegou aqui, o usuário foi cadastrado com sucesso.
            // Define uma mensagem de sucesso na sessão para ser exibida na página de login.
            $_SESSION['success_message'] = "Cadastro realizado com sucesso! Faça o login.";
            // Redireciona o usuário para a página de login (index.php).
            header('Location: ../public/index.php');
            // Termina a execução do script após o redirecionamento.
            exit();

        } catch (PDOException $e) {
            // Se ocorrer um erro durante a inserção no banco:
            // Define uma mensagem de erro na sessão. Em produção, logar $e->getMessage().
            $_SESSION['error_message'] = "Erro ao cadastrar usuário. Tente novamente mais tarde.";
            // log_error($e->getMessage()); // Função hipotética para logar o erro real.
            // Redireciona de volta para a página de cadastro.
            header('Location: ../public/register.php');
            exit();
        }

    } else {
        // --- Erros de Validação Encontrados ---
        // Se o array $errors não estiver vazio, houve erros de validação.
        // Concatena todos os erros em uma única string HTML (cada erro em uma linha).
        $_SESSION['error_message'] = implode("<br>", $errors);
        // Redireciona de volta para a página de cadastro para exibir os erros.
        header('Location: ../public/register.php');
        exit();
    }

} else {
    // --- Método de Requisição Inválido ---
    // Se a requisição não foi POST, redireciona para a página de cadastro.
    // Isso evita que o script seja acessado diretamente pela URL.
    $_SESSION['error_message'] = "Método de requisição inválido.";
    header('Location: ../public/register.php');
    exit();
}
?>
