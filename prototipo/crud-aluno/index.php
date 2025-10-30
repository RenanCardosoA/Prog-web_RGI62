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
<nav class="navbar navbar-light bg-light">
    <a class="navbar-brand" href="../home/index.html">
        <img src="../img/crud/logo-nav.png" width="30" height="30" class="d-inline-block align-top" alt="logo-nav">
        Sistema de presença com carteirinha
    </a>
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
        session_start();
        $current_user_id = $_SESSION['id_usuario'] ?? null;

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
