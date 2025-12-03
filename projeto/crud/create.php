<?php
session_start();

$current_user_id = $_SESSION['id_usuario'] ?? null;

$usuario = $current_user_id;
$aluno = $turma = $status = $data = "";
$errorMessage = $successMessage = "";

require(__DIR__ . '/../connect/index.php');

try {
    $alunos = $conn->query("SELECT id_aluno, nome, id_turma FROM aluno ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
    $turmas = $conn->query("SELECT id_turma, nome_turma FROM turma ORDER BY nome_turma ASC")->fetchAll(PDO::FETCH_ASSOC);
    $usuarios = $conn->query("SELECT id_usuario, nome FROM usuario ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar dados: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aluno = $_POST['aluno'] ?? null;
    $turma = $_POST['turma'] ?? null;
    $data = $_POST['data'] ?? null;
    $status = $_POST['status'] ?? 'presente';

    $usuario = $current_user_id !== null ? $current_user_id : ($_POST['usuario'] ?? null);

    if (!empty($aluno) && !empty($turma)) {
        try {
            $check = $conn->prepare("SELECT COUNT(*) FROM aluno WHERE id_aluno = :id_aluno AND id_turma = :id_turma");
            $check->execute([':id_aluno' => $aluno, ':id_turma' => $turma]);
            if ($check->fetchColumn() == 0) {
                $errorMessage = "O aluno selecionado não pertence à turma escolhida.";
            }
        } catch (PDOException $e) {
            $errorMessage = "Erro ao validar aluno/turma: " . $e->getMessage();
        }
    }

    if ($errorMessage === '') {
        if (empty($aluno) || empty($turma) || empty($data)) {
            $errorMessage = "Aluno, Turma e Data são obrigatórios.";
        } else {
            try {
                $stmt = $conn->prepare("
                    INSERT INTO presenca (id_aluno, id_turma, data_presenca, hora, status, id_usuario)
                    VALUES (:aluno, :turma, :data, NOW(), :status, :usuario)
                ");
                $stmt->execute([
                    ':aluno' => $aluno,
                    ':turma' => $turma,
                    ':data' => $data,
                    ':status' => $status,
                    ':usuario' => $usuario
                ]);
                $successMessage = "Presença registrada com sucesso.";
                $aluno = $turma = $status = $data = "";
                if ($current_user_id === null) $usuario = null;
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $errorMessage = "Já existe presença registrada para esse aluno/turma/data.";
                } else {
                    $errorMessage = "Erro ao registrar presença: " . $e->getMessage();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registrar Presença</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
<link rel="shortcut icon" href="../img/ico/favicon.ico" type="image/x-icon">
</head>
<body>
<div class="container my-5">
    <h2>Registrar Presença</h2>

    <?php if($errorMessage): ?>
        <div class="alert alert-warning alert-dismissible fade show">
            <?= htmlspecialchars($errorMessage) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if($successMessage): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($successMessage) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="post" id="presencaForm">
        <div class="mb-3">
            <label class="form-label">Turma</label>
            <select class="form-select" name="turma" id="selectTurma" required>
                <option value="">Selecione</option>
                <?php foreach($turmas as $t): ?>
                    <option value="<?= $t['id_turma'] ?>" <?= ($turma == $t['id_turma']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['nome_turma']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Aluno</label>
            <select class="form-select" name="aluno" id="selectAluno" required>
                <option value="">Selecione</option>
                <?php foreach($alunos as $a): ?>
                    <option value="<?= $a['id_aluno'] ?>"
                            data-turma="<?= $a['id_turma'] ?>"
                            <?= ($aluno == $a['id_aluno']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($a['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="form-text" id="alunoHelp">Escolha a turma primeiro para filtrar os alunos.</div>
        </div>

        <div class="mb-3">
            <label class="form-label">Data</label>
            <input type="date" class="form-control" name="data" value="<?= htmlspecialchars($data) ?>" required>
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
            <?php if ($current_user_id !== null): ?>
                <?php
                    $nomeUsuario = null;
                    foreach ($usuarios as $u) { if ($u['id_usuario'] == $current_user_id) { $nomeUsuario = $u['nome']; break; } }
                ?>
                <input type="text" class="form-control" value="<?= htmlspecialchars($nomeUsuario ?? 'Usuário logado') ?>" disabled>
                <input type="hidden" name="usuario" value="<?= htmlspecialchars($current_user_id) ?>">
            <?php else: ?>
                <select class="form-select" name="usuario">
                    <option value="">—</option>
                    <?php foreach($usuarios as $u): ?>
                        <option value="<?= $u['id_usuario'] ?>" <?= ($usuario == $u['id_usuario']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
    </form>
</div>

<script>
(function(){
    const selectTurma = document.getElementById('selectTurma');
    const selectAluno = document.getElementById('selectAluno');
    const alunoHelp = document.getElementById('alunoHelp');

    const allAlunoOptions = Array.from(selectAluno.options).map(opt => opt.cloneNode(true));

    function filterAlunos() {
        const turmaSelecionada = selectTurma.value;
        selectAluno.innerHTML = '';

        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = 'Selecione';
        selectAluno.appendChild(placeholder);

        const optionsToShow = allAlunoOptions.filter(opt => {
            const optTurma = opt.getAttribute('data-turma') || '';
            if (!turmaSelecionada) return true;
            return String(optTurma) === String(turmaSelecionada);
        });

        optionsToShow.forEach(opt => selectAluno.appendChild(opt.cloneNode(true)));

        const selectedValue = "<?= htmlspecialchars($aluno) ?>";
        if (selectedValue) {
            const stillExists = Array.from(selectAluno.options).some(o => o.value === selectedValue);
            if (stillExists) {
                selectAluno.value = selectedValue;
            } else {
                selectAluno.value = ''; // reseta
            }
        } else {

        }

        if (!turmaSelecionada) {
            alunoHelp.textContent = 'Escolha a turma primeiro para filtrar os alunos (ou deixe em branco para ver todos).';
        } else {
            alunoHelp.textContent = 'Listando alunos da turma selecionada.';
        }
    }

    selectTurma.addEventListener('change', filterAlunos);

    document.addEventListener('DOMContentLoaded', function(){
        filterAlunos();
    });

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        filterAlunos();
    }
})();
</script>

</body>
</html>
