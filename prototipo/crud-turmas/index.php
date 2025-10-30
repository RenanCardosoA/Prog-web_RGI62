<?php
session_start();
$current_user_id = $_SESSION['id_usuario'] ?? null;

require(__DIR__ . '/../connect/index.php');

// Listar turmas com info do professor
$sql = "SELECT t.id_turma, t.nome_turma, t.curso, t.turno, p.nome AS professor_nome
        FROM turma t
        LEFT JOIN professor p ON t.id_professor = p.id_professor
        ORDER BY t.nome_turma ASC";
$turmas = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Área Administrativa - Turmas</title>
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
<h2>Turmas</h2>
<a href="create.php" class="btn btn-primary mb-3">Adicionar Turma</a>

<table class="table table-bordered">
<thead>
<tr>
    <th>ID</th>
    <th>Nome</th>
    <th>Curso</th>
    <th>Turno</th>
    <th>Professor</th>
    <th>Ação</th>
</tr>
</thead>
<tbody>
<?php foreach($turmas as $t): ?>
<tr>
    <td><?= $t['id_turma'] ?></td>
    <td><?= htmlspecialchars($t['nome_turma']) ?></td>
    <td><?= htmlspecialchars($t['curso']) ?></td>
    <td><?= htmlspecialchars($t['turno']) ?></td>
    <td><?= htmlspecialchars($t['professor_nome']) ?></td>
    <td>
        <a href="edit.php?id=<?= $t['id_turma'] ?>" class="btn btn-primary btn-sm">Editar</a>
        <a href="delete.php?id=<?= $t['id_turma'] ?>" class="btn btn-danger btn-sm">Deletar</a>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</body>
</html>
