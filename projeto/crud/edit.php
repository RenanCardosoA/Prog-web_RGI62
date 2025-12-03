<?php
session_start();
$current_user_id = $_SESSION['id_usuario'] ?? null;

require(__DIR__ . '/../connect/index.php');

$errorMessage = $successMessage = "";

$alunos = $conn->query("SELECT id_aluno, nome FROM aluno ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
$turmas = $conn->query("SELECT id_turma, nome_turma FROM turma ORDER BY nome_turma ASC")->fetchAll(PDO::FETCH_ASSOC);
$usuarios = $conn->query("SELECT id_usuario, nome FROM usuario ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['id'])) {
        header("Location: index.php");
        exit;
    }

    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM presenca WHERE id_presenca = :id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        header("Location: index.php");
        exit;
    }

    $aluno = $row['id_aluno'];
    $turma = $row['id_turma'];
    $data = $row['data_presenca'];
    $status = $row['status'];
    $usuario = $row['id_usuario'];

} else {
    $id = $_POST['id'];
    $aluno = $_POST['aluno'];
    $turma = $_POST['turma'];
    $data = $_POST['data_presenca'];
    $status = $_POST['status'];
    $usuario = $_POST['usuario'] ?: null;

    do {
        if (empty($id) || empty($aluno) || empty($turma) || empty($data)) {
            $errorMessage = "Aluno, Turma e Data são obrigatórios.";
            break;
        }

        $stmt = $conn->prepare("
            UPDATE presenca 
            SET id_aluno = :aluno, id_turma = :turma, data_presenca = :data, 
                status = :status, id_usuario = :usuario
            WHERE id_presenca = :id
        ");
        $stmt->execute([
            ':aluno' => $aluno,
            ':turma' => $turma,
            ':data' => $data,
            ':status' => $status,
            ':usuario' => $usuario,
            ':id' => $id
        ]);

        $successMessage = "Presença atualizada com sucesso!";
        header("Location: index.php");
        exit;

    } while(false);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Editar Presença</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
<link rel="shortcut icon" href="../img/ico/favicon.ico" type="image/x-icon">
</head>
<body>
<div class="container my-5">
<h2 class="mb-4">Editar Presença</h2>

<?php if(!empty($errorMessage)): ?>
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong><?= $errorMessage ?></strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="id" value="<?= $id ?>">

    <div class="mb-3">
        <label class="form-label">Aluno</label>
        <select class="form-select" name="aluno">
            <option value="">Selecione</option>
            <?php foreach($alunos as $a): ?>
                <option value="<?= $a['id_aluno'] ?>" <?= ($aluno==$a['id_aluno'])?'selected':'' ?>><?= htmlspecialchars($a['nome']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Turma</label>
        <select class="form-select" name="turma">
            <option value="">Selecione</option>
            <?php foreach($turmas as $t): ?>
                <option value="<?= $t['id_turma'] ?>" <?= ($turma==$t['id_turma'])?'selected':'' ?>><?= htmlspecialchars($t['nome_turma']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Data</label>
        <input type="date" class="form-control" name="data_presenca" value="<?= htmlspecialchars($data) ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Status</label>
        <select class="form-select" name="status">
            <option value="presente" <?= ($status=='presente')?'selected':'' ?>>Presente</option>
            <option value="falta" <?= ($status=='falta')?'selected':'' ?>>Falta</option>
            <option value="atraso" <?= ($status=='atraso')?'selected':'' ?>>Atraso</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Usuário</label>
        <select class="form-select" name="usuario">
            <option value="">—</option>
            <?php foreach($usuarios as $u): ?>
                <option value="<?= $u['id_usuario'] ?>" <?= ($usuario==$u['id_usuario'])?'selected':'' ?>><?= htmlspecialchars($u['nome']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit" class="btn btn-success">Salvar</button>
    <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
</form>
</div>
</body>
</html>
