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
<nav class="navbar navbar-light bg-light px-3">
  <a class="navbar-brand d-flex align-items-center" href="../home/index.html">
    <img src="../img/crud/logo-nav.png" width="30" height="30" class="d-inline-block align-top me-2" alt="logo-nav">
    Sistema de presença com carteirinha
  </a>

  <div class="d-flex align-items-center">
    <ul class="nav me-3">
      <li class="nav-item">
        <a class="nav-link" href="../crud-aluno/index.php">Alunos</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="../crud-usuarios/index.php">Usuários</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="../crud-turmas/index.php">Turmas</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="../crud/index.php">Presenças</a>
      </li>
    </ul>

    <span class="me-2">Olá, <?= htmlspecialchars($_SESSION['nome_usuario'] ?? 'Usuário') ?></span>
    <a class="btn btn-outline-secondary btn-sm" href="../crud-usuarios/logout.php">Sair</a>
  </div>
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
