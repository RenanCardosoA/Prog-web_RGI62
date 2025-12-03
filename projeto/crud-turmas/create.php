<?php
session_start();
$current_user_id = $_SESSION['id_usuario'] ?? null;

$nome_turma = $curso = $turno = "";
$errorMessage = $successMessage = "";

require(__DIR__ . '/../connect/index.php');

$professores = $conn->query("SELECT id_professor, nome FROM professor ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_turma = trim($_POST['nome_turma']);
    $curso = trim($_POST['curso']);
    $turno = $_POST['turno'];

    if (empty($nome_turma)) {
        $errorMessage = "Nome da turma é obrigatório.";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO turma (nome_turma, curso, turno)
            VALUES (:nome_turma, :curso, :turno)
        ");
        $stmt->execute([
            ':nome_turma' => $nome_turma,
            ':curso' => $curso ?: null,
            ':turno' => $turno,
        ]);
        $successMessage = "Turma cadastrada com sucesso!";
        $nome_turma = $curso = $turno = $professor = "";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cadastrar Turma</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
<link rel="shortcut icon" href="../img/ico/favicon.ico" type="image/x-icon">
</head>
<body>
<div class="container my-5">
<h2>Cadastrar Turma</h2>

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
        <label class="form-label">Nome da Turma *</label>
        <input type="text" class="form-control" name="nome_turma" value="<?= htmlspecialchars($nome_turma) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Curso</label>
        <input type="text" class="form-control" name="curso" value="<?= htmlspecialchars($curso) ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Turno</label>
        <select class="form-select" name="turno">
            <option value="manhã" <?= $turno=='manhã'?'selected':'' ?>>Manhã</option>
            <option value="tarde" <?= $turno=='tarde'?'selected':'' ?>>Tarde</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Salvar</button>
    <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
</form>
</div>
</body>
</html>
