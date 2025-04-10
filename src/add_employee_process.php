<?php
// src/add_employee_process.php

// Inicia a sessão para mensagens de feedback
session_start();

// Inclui a conexão com o banco de dados
require_once '../config/db.php'; // $pdo estará disponível

// Verifica se o usuário está logado (importante para segurança)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Acesso negado.";
    header('Location: ../public/index.php'); // Redireciona para login se não estiver logado
    exit();
}

// Verifica se a requisição foi feita via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Coleta e Limpeza dos Dados ---
    // trim() remove espaços extras
    // Use null coalescing operator (??) ou isset() para campos opcionais
    $name = trim($_POST['name']);
    $position = !empty(trim($_POST['position'])) ? trim($_POST['position']) : null;
    $department = !empty(trim($_POST['department'])) ? trim($_POST['department']) : null;
    $hire_date = !empty(trim($_POST['hire_date'])) ? trim($_POST['hire_date']) : null;
    $salary = !empty(trim($_POST['salary'])) ? trim($_POST['salary']) : null;

    // --- Validação ---
    $errors = [];
    if (empty($name)) {
        $errors[] = "O nome do funcionário é obrigatório.";
    }
    // Validação de data (formato YYYY-MM-DD esperado do input type="date")
    if ($hire_date && !preg_match("/^\d{4}-\d{2}-\d{2}$/", $hire_date)) {
        $errors[] = "Formato de data de contratação inválido.";
        $hire_date = null; // Invalida a data se o formato estiver errado
    }
    // Validação de salário (deve ser numérico e não negativo)
    if ($salary !== null && (!is_numeric($salary) || $salary < 0)) {
        $errors[] = "O salário deve ser um valor numérico não negativo.";
        $salary = null; // Invalida o salário se não for válido
    }

    // Se não houver erros de validação
    if (empty($errors)) {
        try {
            // --- Inserção no Banco de Dados ---
            $sql = "INSERT INTO employees (name, position, department, hire_date, salary)
                    VALUES (:name, :position, :department, :hire_date, :salary)";
            $stmt = $pdo->prepare($sql);

            // Associa os valores aos placeholders
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':position', $position, PDO::PARAM_STR);
            $stmt->bindParam(':department', $department, PDO::PARAM_STR);
            $stmt->bindParam(':hire_date', $hire_date); // PDO::PARAM_STR ou PDO::PARAM_NULL dependendo do valor
            $stmt->bindParam(':salary', $salary); // PDO::PARAM_STR ou PDO::PARAM_NULL dependendo do valor

            // Executa a inserção
            if ($stmt->execute()) {
                // Sucesso: Define mensagem e redireciona
                $_SESSION['success_message'] = "Funcionário adicionado com sucesso!";
            } else {
                // Erro na execução (menos comum se a preparação foi bem-sucedida)
                $_SESSION['error_message'] = "Erro ao adicionar funcionário.";
            }
        } catch (PDOException $e) {
            // Erro na preparação ou execução da query
            $_SESSION['error_message'] = "Erro no banco de dados ao adicionar funcionário: " . $e->getMessage();
            // log_error("Erro DB add employee: " . $e->getMessage()); // Logar erro real
        }
    } else {
        // Se houver erros de validação, junta-os e armazena na sessão
        $_SESSION['error_message'] = implode("<br>", $errors);
    }

    // Redireciona de volta para a página de gerenciamento de funcionários
    header('Location: ../public/employees.php');
    exit();
} else {
    // Se o método não for POST, redireciona por segurança
    $_SESSION['error_message'] = "Método de requisição inválido.";
    header('Location: ../public/employees.php');
    exit();
}
