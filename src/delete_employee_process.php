<?php
// src/delete_employee_process.php

session_start();
require_once '../config/db.php'; // $pdo

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Acesso negado.";
    header('Location: ../public/index.php');
    exit();
}

// --- Obtenção do ID ---
// Verifica se o ID do funcionário foi passado via GET e se é um número inteiro positivo
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
    $employee_id = $_GET['id'];

    // --- Exclusão no Banco de Dados ---
    try {
        // Prepara a instrução DELETE
        $sql = "DELETE FROM employees WHERE id = :id";
        $stmt = $pdo->prepare($sql);

        // Associa o ID ao placeholder
        $stmt->bindParam(':id', $employee_id, PDO::PARAM_INT);

        // Executa a exclusão
        if ($stmt->execute()) {
            // Verifica se alguma linha foi realmente afetada (se o ID existia)
            if ($stmt->rowCount() > 0) {
                $_SESSION['success_message'] = "Funcionário excluído com sucesso!";
            } else {
                // Nenhuma linha afetada, talvez o ID não existisse mais
                $_SESSION['error_message'] = "Funcionário não encontrado ou já excluído.";
            }
        } else {
            // Erro na execução
            $_SESSION['error_message'] = "Erro ao tentar excluir o funcionário.";
        }

    } catch (PDOException $e) {
        // Erro de banco de dados
        $_SESSION['error_message'] = "Erro no banco de dados ao excluir funcionário: " . $e->getMessage();
        // log_error("Erro DB delete employee: " . $e->getMessage()); // Logar erro real
    }

} else {
    // ID inválido ou não fornecido
    $_SESSION['error_message'] = "ID de funcionário inválido para exclusão.";
}

// Redireciona de volta para a página de gerenciamento
header('Location: ../public/employees.php');
exit();
?>
