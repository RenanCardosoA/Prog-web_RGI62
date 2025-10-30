<?php
// /prototipo/crud/index.php
session_start();

// proteção simples: só permite acesso se houver sessão
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../crud-usuarios/login.php");
    exit;
}

try {
    require(__DIR__ . '/../connect/index.php'); // seu arquivo de conexão deve definir $conn (PDO)
    $sql = "SELECT p.id_presenca, a.nome AS aluno_nome, t.nome_turma AS turma_nome, 
                   p.data_presenca, p.hora, p.status, u.nome AS usuario_nome
            FROM presenca p
            JOIN aluno a ON p.id_aluno = a.id_aluno
            JOIN turma t ON p.id_turma = t.id_turma
            LEFT JOIN usuario u ON p.id_usuario = u.id_usuario
            ORDER BY p.data_presenca DESC, p.hora DESC";
    $stmt = $conn->query($sql);
    $presencas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // em produção troque por um erro amigável
    die("Erro na consulta: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Área Administrativa - Presenças</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
<link rel="shortcut icon" href="../img/ico/favicon.ico" type="image/x-icon">
</head>
<body>
<nav class="navbar navbar-light bg-light px-3">
  <a class="navbar-brand d-flex align-items-center" href="../home/index.php">
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
    <span class="me-2">Olá, <?= htmlspecialchars($_SESSION['nome_usuario'] ?? 'Usuário') ?></span>
    <a class="btn btn-outline-secondary btn-sm" href="../crud-usuarios/logout.php">Sair</a>
  </div>
</nav>

<div class="container my-5">
    <h2>Lista de Presenças</h2>
    <a class="btn btn-primary mb-3" href="create.php">Adicionar Presença</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Aluno</th>
                <th>Turma</th>
                <th>Data</th>
                <th>Hora</th>
                <th>Status</th>
                <th>Usuário</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($presencas)): ?>
            <tr><td colspan="8" class="text-center">Nenhuma presença registrada.</td></tr>
        <?php else: ?>
            <?php foreach ($presencas as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id_presenca']) ?></td>
                    <td><?= htmlspecialchars($row['aluno_nome']) ?></td>
                    <td><?= htmlspecialchars($row['turma_nome']) ?></td>
                    <td><?= htmlspecialchars($row['data_presenca']) ?></td>
                    <td><?= htmlspecialchars($row['hora']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td><?= htmlspecialchars($row['usuario_nome'] ?? '—') ?></td>
                    <td>
                        <a class="btn btn-primary btn-sm" href="edit.php?id=<?= urlencode($row['id_presenca']) ?>">Editar</a>
                        <a class="btn btn-danger btn-sm" href="delete.php?id=<?= urlencode($row['id_presenca']) ?>" onclick="return confirm('Confirmar exclusão?')">Deletar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
