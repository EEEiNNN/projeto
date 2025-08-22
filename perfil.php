<?php
include 'header.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];
// Query atualizada para buscar o endereço com JOIN
$stmt = $pdo->prepare("
    SELECT u.nome, u.email, u.telefone, 
           e.id as endereco_id, e.cep, e.rua, e.numero, e.complemento, e.bairro, e.cidade, e.estado
    FROM usuarios u
    LEFT JOIN endereco e ON u.endereco_id = e.id
    WHERE u.id = ?
");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$feedback = $_SESSION['feedback'] ?? null;
unset($_SESSION['feedback']);
?>

<title>Meu Perfil | Ben-David</title>
<link rel="stylesheet" href="_css/perfil.css">

<main>
    <section class="profile-section">
        <div class="profile-container">
            <h1 class="profile-header">Meu Perfil</h1>

            <?php if ($feedback): ?>
                <div class="feedback-message <?php echo $feedback['type'] === 'success' ? 'feedback-success' : 'feedback-error'; ?>">
                    <?php echo htmlspecialchars($feedback['message']); ?>
                </div>
            <?php endif; ?>

            <form action="atualizar_perfil.php" method="POST" class="profile-form">
                <input type="hidden" name="action" value="update_details">
                <input type="hidden" name="endereco_id" value="<?php echo $user['endereco_id'] ?? ''; ?>">
                <h2>Dados Pessoais</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nome">Nome Completo</label>
                        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                    </div>
                </div>
                <div class="form-group full-width">
                    <label for="telefone">Telefone</label>
                    <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($user['telefone'] ?? ''); ?>">
                </div>

                <h2 style="margin-top: 2rem;">Meu Endereço</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="cep">CEP</label>
                        <input type="text" id="cep" name="cep" value="<?php echo htmlspecialchars($user['cep'] ?? ''); ?>">
                    </div>
                     <div class="form-group">
                        <label for="rua">Rua</label>
                        <input type="text" id="rua" name="rua" value="<?php echo htmlspecialchars($user['rua'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="numero">Número</label>
                        <input type="text" id="numero" name="numero" value="<?php echo htmlspecialchars($user['numero'] ?? ''); ?>">
                    </div>
                     <div class="form-group">
                        <label for="bairro">Bairro</label>
                        <input type="text" id="bairro" name="bairro" value="<?php echo htmlspecialchars($user['bairro'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="cidade">Cidade</label>
                        <input type="text" id="cidade" name="cidade" value="<?php echo htmlspecialchars($user['cidade'] ?? ''); ?>">
                    </div>
                     <div class="form-group">
                        <label for="estado">Estado</label>
                        <input type="text" id="estado" name="estado" maxlength="2" value="<?php echo htmlspecialchars($user['estado'] ?? ''); ?>">
                    </div>
                </div>
                <button type="submit" class="btn-submit">Salvar Alterações</button>
            </form>

            <form action="atualizar_perfil.php" method="POST" class="profile-form">
                </form>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>