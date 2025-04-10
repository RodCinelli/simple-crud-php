// public/js/script.js

// Exemplo: Adicionar validação de confirmação de senha no lado do cliente

// Aguarda o DOM (estrutura da página) estar completamente carregado.
document.addEventListener('DOMContentLoaded', function() {

    // Tenta encontrar o formulário de cadastro pelo seu action attribute.
    // Ajuste o seletor se necessário. Uma ID no form seria mais robusto: document.getElementById('register-form')
    const registerForm = document.querySelector('form[action="../src/register_process.php"]');

    // Se o formulário de cadastro existir na página atual...
    if (registerForm) {
        // Encontra os campos de senha e confirmação de senha dentro do formulário.
        const passwordInput = registerForm.querySelector('#password');
        const confirmPasswordInput = registerForm.querySelector('#confirm_password');

        // Adiciona um ouvinte de evento para quando o formulário for submetido.
        registerForm.addEventListener('submit', function(event) {
            // Verifica se os valores dos campos de senha e confirmação são diferentes.
            if (passwordInput.value !== confirmPasswordInput.value) {
                // Se forem diferentes:
                // 1. Exibe um alerta (melhor seria mostrar uma mensagem de erro na página).
                alert('As senhas não coincidem!');
                // 2. Impede o envio do formulário para o servidor.
                event.preventDefault();
            }
            // Se as senhas coincidirem, o envio do formulário prossegue normalmente.
        });
    }

    // Você pode adicionar mais validações ou interações aqui,
    // como verificar a força da senha, validar formato de email em tempo real, etc.

});
