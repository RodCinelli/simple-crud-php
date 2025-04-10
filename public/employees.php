<?php
// public/employees.php

// Inicia ou resume a sessão
session_start();

// Verifica se o usuário está logado, senão redireciona para o login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Acesso negado. Faça login para continuar.";
    header('Location: index.php');
    exit();
}

// Inclui a conexão com o banco de dados
require_once '../config/db.php'; // $pdo estará disponível

// --- Lógica para buscar funcionários (Read) ---
$employees = []; // Inicializa um array vazio para os funcionários
try {
    // Prepara a consulta SQL para selecionar todos os funcionários, ordenados por nome
    $stmt = $pdo->query("SELECT id, name, position, department, hire_date, salary FROM employees ORDER BY name ASC");
    // Executa a consulta e busca todos os resultados como um array associativo
    $employees = $stmt->fetchAll();
} catch (PDOException $e) {
    // Em caso de erro na consulta, armazena a mensagem de erro na sessão
    // Em produção, logar o erro em vez de mostrá-lo diretamente
    $_SESSION['error_message'] = "Erro ao buscar funcionários: " . $e->getMessage();
    // log_error("Erro ao buscar funcionários: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Funcionários</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container container-large">
        <a href="dashboard.php" class="back-link">&larr; Voltar ao Painel</a>
        <h2>Gerenciar Funcionários</h2>

        <?php
        // Exibe mensagens de erro ou sucesso da sessão
        if (isset($_SESSION['error_message'])) {
            echo '<div class="error-message">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
            unset($_SESSION['error_message']); // Limpa a mensagem
        }
        if (isset($_SESSION['success_message'])) {
            echo '<div class="success-message">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
            unset($_SESSION['success_message']); // Limpa a mensagem
        }
        ?>

        <div class="add-form">
            <h3>Adicionar Novo Funcionário</h3>
            <form action="../src/add_employee_process.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Nome Completo:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="position">Cargo:</label>
                        <input type="text" id="position" name="position">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="department">Departamento:</label>
                        <input type="text" id="department" name="department">
                    </div>
                    <div class="form-group">
                        <label for="hire_date">Data de Contratação:</label>
                        <input type="date" id="hire_date" name="hire_date">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="salary">Salário (R$):</label>
                        <input type="number" id="salary" name="salary" step="0.01" min="0">
                    </div>
                    <div class="form-group" style="flex-grow: 1;"></div>
                </div>
                <button type="submit">Adicionar Funcionário</button>
            </form>
        </div>

        <h3>Lista de Funcionários</h3>
        <?php if (count($employees) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Cargo</th>
                        <th>Departamento</th>
                        <th>Contratação</th>
                        <th>Salário (R$)</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $employee): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($employee['id']); ?></td>
                            <td><?php echo htmlspecialchars($employee['name']); ?></td>
                            <td><?php echo htmlspecialchars($employee['position'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($employee['department'] ?? '-'); ?></td>
                            <td><?php echo $employee['hire_date'] ? htmlspecialchars(date('d/m/Y', strtotime($employee['hire_date']))) : '-'; ?></td>
                            <td><?php echo $employee['salary'] ? htmlspecialchars(number_format($employee['salary'], 2, ',', '.')) : '-'; ?></td>
                            <td class="action-links">
                                <a href="edit_employee.php?id=<?php echo $employee['id']; ?>" class="edit-link">Editar</a>
                                <a href="../src/delete_employee_process.php?id=<?php echo $employee['id']; ?>" class="delete-link" onclick="return confirm('Tem certeza que deseja excluir este funcionário?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum funcionário cadastrado ainda.</p>
        <?php endif; ?>

    </div>

</body>

</html>