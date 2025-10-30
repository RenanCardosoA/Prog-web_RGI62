<?php
session_start();
$current_user_id = $_SESSION['id_usuario'] ?? null;

require(__DIR__ . '/../connect/index.php');

$usuarios = $conn->query("SELECT * FROM usuario ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Usuários</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
<link rel="shortcut icon" href="../img/ico/favicon.ico" type="image/x-icon">
</head>
<body>
<nav class="navbar navbar-light bg-light">
    <a class="navbar-brand" href="../home/index.html">
        <img src="../img/crud/logo-nav.png" width="30" height="30" class="d-inline-block align-top" alt="logo-nav">
        Sistema de presença com carteirinha
    </a>
</nav>
<div class="container my-5">
<h2>Usuários</h2>
<a href="create.php" class="btn btn-primary mb-3">Adicionar Usuário</a>

<table class="table table-bordered">
<thead>
<tr>
    <th>ID</th>
    <th>Nome</th>
    <th>Email</th>
    <th>Tipo</th>
    <th>Ação</th>
</tr>
</thead>
<tbody>
<?php foreach($usuarios as $u): ?>
<tr>
    <td><?= $u['id_usuario'] ?></td>
    <td><?= htmlspecialchars($u['nome']) ?></td>
    <td><?= htmlspecialchars($u['email']) ?></td>
    <td><?= htmlspecialchars($u['tipo']) ?></td>
    <td>
        <a href="edit.php?id=<?= $u['id_usuario'] ?>" class="btn btn-primary btn-sm">Editar</a>
        <a href="delete.php?id=<?= $u['id_usuario'] ?>" class="btn btn-danger btn-sm">Deletar</a>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</body>
</html>
