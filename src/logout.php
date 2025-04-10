<?php
// src/logout.php

// Inicia ou resume a sessão existente para poder modificá-la.
session_start();

// --- Limpeza das Variáveis de Sessão ---
// Remove todas as variáveis armazenadas na sessão.
// Isso garante que dados como 'user_id' e 'username' sejam apagados.
$_SESSION = array();

// --- Destruição do Cookie de Sessão (Opcional, mas recomendado) ---
// Se você estiver usando cookies para armazenar o ID da sessão (comportamento padrão),
// é uma boa prática remover o cookie do navegador do usuário também.
// Verifica se os parâmetros de cookie da sessão existem.
if (ini_get("session.use_cookies")) {
    // Obtém os parâmetros do cookie de sessão atual.
    $params = session_get_cookie_params();
    // Define um cookie com o mesmo nome, mas com um tempo de expiração no passado (-42000 segundos).
    // Isso instrui o navegador a excluir o cookie.
    // Define também o path, domain, secure e httponly com os mesmos valores
    // do cookie original para garantir que o cookie correto seja excluído.
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// --- Destruição da Sessão no Servidor ---
// Destrói completamente a sessão no lado do servidor.
// Isso invalida o ID de sessão atual.
session_destroy();

// --- Redirecionamento ---
// Redireciona o usuário de volta para a página de login (index.php).
header('Location: ../public/index.php');
// Termina a execução do script após o redirecionamento.
exit();
?>
