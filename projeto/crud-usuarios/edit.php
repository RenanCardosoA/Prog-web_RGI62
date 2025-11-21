<?php
session_start();
require(__DIR__ . '/../connect/index.php');

// vai verificar se fez login mesmo ou não
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

// Busca os dados e campos do usuário
$stmt = $conn->prepare("SELECT * FROM usuario WHERE id_usuario = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "Usuário não encontrado.";
    exit;
}

// Atualiza se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $senha = $_POST['senha'] ?? '';

    try {
        if (!empty($senha)) {
            // vai atualizar a senha se houver alterações no campo
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE usuario SET nome = ?, email = ?, tipo = ?, senha = ? WHERE id_usuario = ?");
            $stmt->execute([$nome, $email, $tipo, $senhaHash, $id]);
        } else {
            // Não atualiza senha
            $stmt = $conn->prepare("UPDATE usuario SET nome = ?, email = ?, tipo = ? WHERE id_usuario = ?");
            $stmt->execute([$nome, $email, $tipo, $id]);
        }

        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        echo "Erro ao atualizar: " . $e->getMessage();
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
</head>
<body>
<div class="container my-5">
    <h2>Editar Usuário</h2>
    <form method="post">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome:</label>
            <input type="text" name="nome" id="nome" class="form-control" value="<?= $usuario['nome'] ?>" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" id="email" class="form-control" value="<?= $usuario['email'] ?>" required>
        </div>

        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo de Usuário:</label>
            <select name="tipo" id="tipo" class="form-select" required>
                <option value="admin" <?= $usuario['tipo'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                <option value="professor" <?= $usuario['tipo'] === 'professor' ? 'selected' : '' ?>>professor</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="senha" class="form-label">Nova Senha (opcional):</label>
            <input type="password" name="senha" id="senha" class="form-control" placeholder="Deixe em branco para manter a senha atual">
        </div>

        <button type="submit" class="btn btn-success">Salvar Alterações</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
