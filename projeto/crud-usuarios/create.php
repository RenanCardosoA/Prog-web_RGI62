<?php
session_start();
$current_user_id = $_SESSION['id_usuario'] ?? null;

$nome = $email = $senha = $tipo = "";
$errorMessage = $successMessage = "";

require(__DIR__ . '/../connect/index.php');

if($_SERVER['REQUEST_METHOD']==='POST'){
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $tipo = $_POST['tipo'];

    if(empty($nome) || empty($email) || empty($senha)){
        $errorMessage = "Nome, Email e Senha são obrigatórios.";
    } else {
        $senha_hash = ($senha);
        $stmt = $conn->prepare("INSERT INTO usuario (nome, email, senha, tipo) VALUES (:nome, :email, :senha, :tipo)");
        $stmt->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':senha' => $senha,
            ':tipo' => $tipo
        ]);
        $successMessage = "Usuário cadastrado com sucesso!";
        $nome = $email = $senha = $tipo = "";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cadastrar Usuário</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
<link rel="shortcut icon" href="../img/ico/favicon.ico" type="image/x-icon">
</head>
<body>
<div class="container my-5">
<h2>Cadastrar Usuário</h2>

<?php if($errorMessage): ?>
<div class="alert alert-warning alert-dismissible fade show"><?= $errorMessage ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if($successMessage): ?>
<div class="alert alert-success alert-dismissible fade show"><?= $successMessage ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<form method="post">
<div class="mb-3">
    <label class="form-label">Nome *</label>
    <input type="text" class="form-control" name="nome" value="<?= $nome ?>" required>
</div>

<div class="mb-3">
    <label class="form-label">E-mail *</label>
    <input type="email" class="form-control" name="email" value="<?= $email ?>" required>
</div>

<div class="mb-3">
    <label class="form-label">Senha *</label>
    <input type="password" class="form-control" name="senha" required>
</div>

<div class="mb-3">
    <label class="form-label">Tipo</label>
    <select class="form-select" name="tipo">
        <option value="admin" <?= $tipo=='admin'?'selected':'' ?>>Admin</option>
        <option value="professor" <?= $tipo=='professor'?'selected':'' ?>>Professor</option>
    </select>
</div>

<button type="submit" class="btn btn-primary">Salvar</button>
<a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
</form>
</div>
</body>
</html>
