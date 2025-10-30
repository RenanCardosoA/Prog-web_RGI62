<?php
// usuarios/login.php
ob_start();
session_start();

$error = "";

try {
    require(__DIR__ . '/../connect/index.php'); // mantém seu require para conexão

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if ($email === '' || $senha === '') {
            $error = "Preencha e-mail e senha.";
        } else {
            $stmt = $conn->prepare("SELECT id_usuario, nome, senha_hash, tipo FROM usuario WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($senha, $user['senha_hash'])) {
                // grava sessão usando as chaves que o index.php espera
                $_SESSION['id_usuario'] = $user['id_usuario'];
                $_SESSION['nome_usuario'] = $user['nome'];
                $_SESSION['tipo_usuario'] = $user['tipo'];

                header("Location: ../crud/index.php");
                exit;
            } else {
                $error = "E-mail ou senha inválidos.";
            }
        }
    }
} catch (PDOException $e) {
    $error = "Erro no banco: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login - Sistema de Presença</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
<link rel="shortcut icon" href="../img/ico/favicon.ico" type="image/x-icon">
</head>
<body>
<div class="container my-5" style="max-width:600px">
    <h3>Login</h3>
    <?php if($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="">
        <div class="mb-3">
            <label class="form-label">E-mail</label>
            <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Senha</label>
            <input type="password" name="senha" class="form-control" required>
        </div>
        <button class="btn btn-primary">Entrar</button>
        <a href="../home/index.html" class="btn btn-outline-secondary">Voltar</a>
    </form>
</div>
</body>
</html>
<?php ob_end_flush(); ?>
