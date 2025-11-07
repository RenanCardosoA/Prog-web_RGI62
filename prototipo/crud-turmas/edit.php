<?php
session_start();
require(__DIR__ . '/../connect/index.php');

// Verifica se foi passado um ID via GET
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id_turma = (int)$_GET['id'];

// Busca os dados da turma
$stmt = $conn->prepare("SELECT * FROM turma WHERE id_turma = ?");
$stmt->execute([$id_turma]);
$turma = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$turma) {
    echo "Turma não encontrada.";
    exit;
}

// Atualiza turma
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_turma = trim($_POST['nome_turma']);
    $curso = trim($_POST['curso']);
    $turno = trim($_POST['turno']);

    if (empty($nome_turma) || empty($curso) || empty($turno)) {
        $erro = "Preencha todos os campos.";
    } else {
        $stmt = $conn->prepare("UPDATE turma SET nome_turma = ?, curso = ?, turno = ? WHERE id_turma = ?");
        $stmt->execute([$nome_turma, $curso, $turno, $id_turma]);

        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Editar Turma</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container my-5">
    <h2>Editar Turma</h2>

    <?php if (!empty($erro)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label for="nome_turma" class="form-label">Nome da Turma:</label>
            <input type="text" name="nome_turma" id="nome_turma" class="form-control" 
                   value="<?= htmlspecialchars($turma['nome_turma']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="curso" class="form-label">Curso:</label>
            <input type="text" name="curso" id="curso" class="form-control" 
                   value="<?= htmlspecialchars($turma['curso']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="turno" class="form-label">Turno:</label>
            <select name="turno" id="turno" class="form-select" required>
                <option value="Manhã" <?= $turma['turno'] === 'Manhã' ? 'selected' : '' ?>>Manhã</option>
                <option value="Tarde" <?= $turma['turno'] === 'Tarde' ? 'selected' : '' ?>>Tarde</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Salvar Alterações</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
