<?php
session_start();
$current_user_id = $_SESSION['id_usuario'] ?? null;

require(__DIR__ . '/../connect/index.php');

$id_usuario = $nome = $email = $tipo = "";
$errorMessage = $successMessage = "";

// GET: carrega os dados do usuário
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['id'])) {
        header("Location: index.php");
        exit;
    }

    $id_usuario = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM usuario WHERE id_usuario = :id");
    $stmt->execute([':id' => $id_usuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header("Location: index.php");
        exit;
    }

    $nome = $user['nome'];
    $email = $user['email'];
    $tipo = $user['tipo'];
} 

// POST: atualiza os dados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_POST['id_usuario'];
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $tipo = $_POST['tipo'];
    $senha = $_POST['senha'];

    if (empty($nome) || empty($email)) {
        $errorMessage = "Nome e E-mail são obrigatórios.";
    } else {
        // Atualiza senha apenas se o campo não estiver vazio
        if (!empty($senha)) {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE usuario SET nome=:nome, email=:email, tipo=:tipo, senha_hash=:senha WHERE id_usuario=:id");
            $stmt->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':tipo' => $tipo,
                ':senha' => $senha_hash,
                ':id' => $id_usuario
            ]);
        } else {
            $stmt = $conn->prepare("UPDATE usuario SET nome=:nome, email=:email, tipo=:tipo WHERE id_usuario=:id");
            $stmt->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':tipo' => $tipo,
                ':id' => $id_usuario
            ]);
        }

        $successMessage = "Usuário atualizado com sucesso!";
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Editar Usuário</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
<link rel="shortcut icon" href="../img/ico/favicon.ico" type="image/x-icon">
</head>
<body>
<div class="container my-5">
<h2>Editar Usuário - <?= htmlspecialchars($nome) ?></h2>

<?php if($errorMessage): ?>
<div class="alert alert-warning alert-dismissible fade show"><?= $errorMessage ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<form method="post">
<input type="hidden" name="id_usuario" value="<?= $id_usuario ?>">

<div class="mb-3">
    <label class="form-label">Nome *</label>
    <input type="text" class="form-control" name="nome" value="<?= htmlspecialchars($nome) ?>" required>
</div>

<div class="mb-3">
    <label class="form-label">E-mail *</label>
    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($email) ?>" required>
</div>

<div class="mb-3">
    <label class="form-label">Tipo</label>
    <select class="form-select" name="tipo">
        <option value="admin" <?= $tipo=='admin'?'selected':'' ?>>Admin</option>
        <option value="professor" <?= $tipo=='professor'?'selected':'' ?>>Professor</option>
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Senha (preencha apenas se quiser alterar)</label>
    <input type="password" class="form-control" name="senha">
</div>

<button type="submit" class="btn btn-primary">Salvar</button>
<a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
</form>
</div>
</body>
</html>
