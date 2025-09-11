<?php
require_once("conexao.php");

try {
    // Verifica se a tabela de usuários está vazia
    $query = $pdo->query("SELECT id FROM usuarios LIMIT 1");
    if ($query->rowCount() == 0) {

        // Inicia uma transação para garantir a consistência dos dados
        $pdo->beginTransaction();

        // 1. Insere o usuário Administrador
        $senha_hash_admin = password_hash('123', PASSWORD_DEFAULT);
        $stmtAdmin = $pdo->prepare(
            "INSERT INTO usuarios (nome, email, senha, nivel, ativo, status) 
             VALUES ('Administrador', 'admin@admin.com', ?, 'admin', 'Sim', 'ativo')"
        );
        $stmtAdmin->execute([$senha_hash_admin]);

        // 2. Insere o usuário comum (User)
        $senha_hash_user = password_hash('123', PASSWORD_DEFAULT);
        $stmtUser = $pdo->prepare(
            "INSERT INTO usuarios (nome, email, senha, nivel, ativo, status) 
             VALUES ('User', 'user@user.com', ?, 'user', 'Sim', 'ativo')"
        );
        $stmtUser->execute([$senha_hash_user]);
        $userId = $pdo->lastInsertId(); // Pega o ID do 'User'

        // 3. Insere o endereço para o 'User'
        $stmtEndereco = $pdo->prepare(
            "INSERT INTO endereco (cep, rua, numero, bairro, estado, cidade, usuario_id)
             VALUES ('70160-900', 'Praça dos Três Poderes', '111', 'Zona Cívico-Administrativa', 'DF', 'Brasília', ?)"
        );
        $stmtEndereco->execute([$userId]);
        $enderecoId = $pdo->lastInsertId(); // Pega o ID do endereço

        // 4. Define os produtos para o pedido de exemplo (baseado no seu .sql)
        $produto1_preco = 350.00;  // Brincos
        $produto2_preco = 1000.00; // Anel
        $total_pedido = $produto1_preco + $produto2_preco;

        // 5. Insere o PEDIDO para o 'User' com o total correto
        $stmtPedido = $pdo->prepare(
            "INSERT INTO pedidos (status, total, data_pedidos, usuario_id, endereco_id) 
             VALUES ('processando', ?, NOW(), ?, ?)"
        );
        $stmtPedido->execute([$total_pedido, $userId, $enderecoId]);
        $pedidoId = $pdo->lastInsertId(); // Pega o ID do pedido

        // 6. **CORREÇÃO**: Insere os ITENS DO PEDIDO na tabela `itempedidos`
        $stmtItens1 = $pdo->prepare(
            "INSERT INTO itempedidos (pedidos_id, produto_id, quantidade, preco) VALUES (?, ?, ?, ?)"
        );
        // Adiciona o produto com ID 2 (Brincos)
        $stmtItens1->execute([$pedidoId, 2, 1, $produto1_preco]); 

        $stmtItens2 = $pdo->prepare(
            "INSERT INTO itempedidos (pedidos_id, produto_id, quantidade, preco) VALUES (?, ?, ?, ?)"
        );
        // Adiciona o produto com ID 1 (Anel)
        $stmtItens2->execute([$pedidoId, 1, 1, $produto2_preco]);

        // Se tudo ocorreu bem, confirma a transação
        $pdo->commit();
    
    }
} catch (PDOException $e) {
    // Se algum erro ocorreu, desfaz tudo
    $pdo->rollBack();
    die("Erro ao inicializar a base de dados: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso ao Sistema</title>
    <link rel="stylesheet" href="_css/login.css">
    <link rel="shortcut icon" type="image/x-icon" href="_img/icone.svg">
    <style>
        .form-container { display: none; }
        .form-container.active { display: block; }
        .tab-buttons { text-align: center; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.2); }
        .tab-button {
            background: none; border: none; color: #8f8f8f; font-size: 18px;
            padding: 10px 15px; cursor: pointer; font-weight: 500; transition: 0.3s;
            border-bottom: 3px solid transparent;
        }
        .tab-button.active { color: #fff; border-bottom-color: #A1CCA5; }
        .feedback-message { 
            text-align: center; margin-top: 15px; padding: 10px; border-radius: 5px;
            font-size: 0.9em; display: none; color: white;
        }
        .feedback-message.success { background-color: #28a745; }
        .feedback-message.error { background-color: #dc3545; }
    </style>
</head>
<body>
    <div class="box" style="height: auto; min-height: 520px;">
        <div class="figura"><a href="index.php">B</a></div>
        <span class="borderLine"></span>
        
        <div class="tab-buttons">
            <button class="tab-button active" data-form="login">Entrar</button>
            <button class="tab-button" data-form="register">Cadastrar</button>
        </div>

        <div id="login-container" class="form-container active">
            <form id="form-login" method="POST">
                <input type="hidden" name="action" value="login">
                <div class="inputBox">
                    <input type="email" name="email" required>
                    <span>Email</span><i></i>
                </div>
                <div class="inputBox">
                    <input type="password" name="senha" required>
                    <span>Senha</span><i></i>
                </div>
                <div class="links"><a href="#">Esqueci a senha</a></div>
                <input type="submit" value="Login">
            </form>
        </div>

        <div id="register-container" class="form-container">
            <form id="form-register" method="POST">
                <div id="register-step1">
                    <p style="color:#fff; font-size: 0.9em; text-align: center; margin-bottom: 20px;">
                        Insira o seu email para começar o registro.
                    </p>
                    <div class="inputBox">
                        <input type="email" name="email" required>
                        <span>Email</span><i></i>
                    </div>
                    <input type="submit" value="Continuar" style="padding: 9px;">
                </div>

                <div id="register-step2" style="display: none;">
                    <div class="inputBox" id="nome-wrapper">
                        <input type="text" name="nome" required>
                        <span>Nome Completo</span><i></i>
                    </div>
                    <div class="inputBox">
                        <input type="password" name="senha" required>
                        <span>Crie uma Senha (mín. 6 caracteres)</span><i></i>
                    </div>
                    <input type="submit" value="Finalizar Cadastro" style="padding: 9px; width: auto;">
                </div>
            </form>
        </div>
        
        <div id="feedback" class="feedback-message"></div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tabButtons = document.querySelectorAll('.tab-button');
    const formContainers = document.querySelectorAll('.form-container');
    const feedbackDiv = document.getElementById('feedback');
    const formLogin = document.getElementById('form-login');
    const formRegister = document.getElementById('form-register');
    const registerStep1 = document.getElementById('register-step1');
    const registerStep2 = document.getElementById('register-step2');
    const emailInputRegister = registerStep1.querySelector('input[name="email"]');
    const nomeWrapper = document.getElementById('nome-wrapper');

    let registerAction = 'check_email'; 

    const manageRequiredAttributes = (activeContainerId) => {
        formContainers.forEach(container => {
            const isContainerActive = container.id === activeContainerId;
            container.querySelectorAll('input[name]').forEach(input => {
                if (isContainerActive && input.offsetParent !== null) {
                    input.setAttribute('required', 'required');
                } else {
                    input.removeAttribute('required');
                }
            });
        });
    };

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const formContainerId = button.dataset.form + '-container';
            
            tabButtons.forEach(btn => btn.classList.remove('active'));
            formContainers.forEach(c => c.classList.remove('active'));
            
            button.classList.add('active');
            document.getElementById(formContainerId).classList.add('active');
            
            feedbackDiv.style.display = 'none';
            manageRequiredAttributes(formContainerId); 
        });
    });

    formLogin.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitButton = formLogin.querySelector('input[type="submit"]');
        const originalButtonText = submitButton.value;
        submitButton.disabled = true;
        submitButton.value = 'Aguarde...';
        
        try {
            const formData = new FormData(formLogin);
            const response = await fetch('autenticar.php', { method: 'POST', body: formData });
            const data = await response.json();

            if (data.success && data.redirect) {
                showFeedback(data.message, true);
                setTimeout(() => { window.location.href = data.redirect; }, 1000);
            } else {
                showFeedback(data.message || 'Ocorreu um erro.', false);
            }
        } catch (error) {
            showFeedback('Erro de comunicação com o servidor.', false);
        } finally {
            submitButton.disabled = false;
            submitButton.value = originalButtonText;
        }
    });

    formRegister.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitButton = e.currentTarget.querySelector('input[type="submit"]:not([style*="display: none"])');
        const originalButtonText = submitButton.value;
        submitButton.disabled = true;
        submitButton.value = 'Aguarde...';

        const formData = new FormData(formRegister);
        formData.append('action', registerAction);

        try {
            const response = await fetch('autenticar.php', { method: 'POST', body: formData });
            const data = await response.json();

            if (data.success) {
                if (registerAction === 'check_email') {
                    registerStep1.style.display = 'none';
                    registerStep2.style.display = 'block';
                    emailInputRegister.readOnly = true;

                    if (data.status === 'pending') {
                        nomeWrapper.style.display = 'none';
                        registerAction = 'set_password';
                    } else { 
                        registerAction = 'register';
                    }
                    manageRequiredAttributes('register-container'); 
                } else { 
                    showFeedback(data.message + ' Redirecionando...', true);
                    setTimeout(() => { window.location.href = 'login.php'; }, 2000);
                }
            } else {
                showFeedback(data.message, false);
            }
        } catch (error) {
            showFeedback('Erro de comunicação com o servidor.', false);
        } finally {
            submitButton.disabled = false;
            submitButton.value = originalButtonText;
        }
    });
    
    const showFeedback = (message, isSuccess) => {
        feedbackDiv.textContent = message;
        feedbackDiv.className = `feedback-message ${isSuccess ? 'success' : 'error'}`;
        feedbackDiv.style.display = 'block';
    };

    manageRequiredAttributes('login-container');
});
</script>
</body>
</html>