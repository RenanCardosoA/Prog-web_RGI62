<?php
session_start();
$current_user_id = $_SESSION['id_usuario'] ?? null;
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../crud-usuarios/login.php');
    exit; 
}

$nome_usuario = htmlspecialchars($_SESSION['nome_usuario'] ?? 'Usuário', ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área Administrativa - Alunos</title>
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
    <h2>Lista de Alunos</h2>
    <a class="btn btn-primary mb-3" href="create.php" role="button">Adicionar Aluno</a>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Matrícula</th>
                <th>Email</th>
                <th>Telefone</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
        <?php

        try {
            require(__DIR__ . '/../connect/index.php');

            $sql = "SELECT * FROM aluno";
            $result = $conn->query($sql);

            while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "
                <tr>
                    <td>{$row['id_aluno']}</td>
                    <td>{$row['nome']}</td>
                    <td>{$row['matricula']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['telefone']}</td>
                    <td>
                        <a class='btn btn-primary btn-sm' href='edit.php?id={$row['id_aluno']}'>Editar</a>
                        <a class='btn btn-danger btn-sm' href='delete.php?id={$row['id_aluno']}'>Deletar</a>
                    </td>
                </tr>
                ";
            }

        } catch(PDOException $e) {
            echo "Erro: " . $e->getMessage();
        }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
