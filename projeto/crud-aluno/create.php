<?php
session_start();
$current_user_id = $_SESSION['id_usuario'] ?? null;

$nome = $matricula = $cpf = $data_nascimento = $email = $telefone = "";
$id_turma = null;
$errorMessage = $successMessage = "";

try {
    require(__DIR__ . '/../connect/index.php');

    // Busca turmas para popular o select
    $turmas = $conn->query("SELECT id_turma, nome_turma FROM turma ORDER BY nome_turma ASC")->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = trim($_POST['nome'] ?? '');
        $matricula = trim($_POST['matricula'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');
        $data_nascimento = $_POST['data_nascimento'] ?? null;
        $email = trim($_POST['email'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $id_turma = $_POST['turma'] ?? null;
        if ($id_turma === '') $id_turma = null; // normaliza

        // Validação básica
        if (empty($nome) || empty($matricula)) {
            $errorMessage = "Nome e Matrícula são obrigatórios.";
        } else {
            // Inserção: inclui id_turma se informar (coluna id_turma deve existir na tabela aluno)
            if ($id_turma !== null) {
                $stmt = $conn->prepare("
                    INSERT INTO aluno (nome, matricula, cpf, data_nascimento, email, telefone, id_turma)
                    VALUES (:nome, :matricula, :cpf, :data_nascimento, :email, :telefone, :id_turma)
                ");
                $params = [
                    ':nome' => $nome,
                    ':matricula' => $matricula,
                    ':cpf' => $cpf ?: null,
                    ':data_nascimento' => $data_nascimento ?: null,
                    ':email' => $email ?: null,
                    ':telefone' => $telefone ?: null,
                    ':id_turma' => $id_turma
                ];
            } else {
                $stmt = $conn->prepare("
                    INSERT INTO aluno (nome, matricula, cpf, data_nascimento, email, telefone)
                    VALUES (:nome, :matricula, :cpf, :data_nascimento, :email, :telefone)
                ");
                $params = [
                    ':nome' => $nome,
                    ':matricula' => $matricula,
                    ':cpf' => $cpf ?: null,
                    ':data_nascimento' => $data_nascimento ?: null,
                    ':email' => $email ?: null,
                    ':telefone' => $telefone ?: null
                ];
            }

            try {
                $stmt->execute($params);

                $successMessage = "Aluno cadastrado com sucesso!";
                // Limpar campos após salvar
                $nome = $matricula = $cpf = $data_nascimento = $email = $telefone = "";
                $id_turma = null;
            } catch (PDOException $e) {
                // Mensagem mais amigável para FK / coluna ausente
                if (strpos($e->getMessage(), 'Unknown column') !== false) {
                    $errorMessage = "Erro no banco: parece que a coluna de turma (id_turma) não existe na tabela aluno. "
                        . "Verifique o esquema do banco ou execute o SQL para adicionar a coluna.";
                } elseif (strpos($e->getMessage(), 'a foreign key constraint') !== false
                       || strpos($e->getMessage(), 'Foreign key') !== false) {
                    $errorMessage = "Erro de integridade referencial: a turma selecionada não existe. "
                        . "Verifique as turmas cadastradas antes de atribuir.";
                } else {
                    $errorMessage = "Erro ao cadastrar aluno: " . $e->getMessage();
                }
            }
        }
    }

} catch (PDOException $e) {
    $errorMessage = "Erro ao conectar / buscar dados: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cadastro de Aluno</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
<link rel="shortcut icon" href="../img/ico/favicon.ico" type="image/x-icon">
</head>
<body>
<div class="container my-5">
<h2>Cadastro de Aluno</h2>

<?php if($errorMessage): ?>
<div class="alert alert-warning alert-dismissible fade show"><?= htmlspecialchars($errorMessage) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if($successMessage): ?>
<div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($successMessage) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<form method="post">
    <div class="mb-3">
        <label class="form-label">Nome *</label>
        <input type="text" class="form-control" name="nome" value="<?= htmlspecialchars($nome) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Matrícula *</label>
        <input type="text" class="form-control" name="matricula" value="<?= htmlspecialchars($matricula) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">CPF</label>
        <input type="text" class="form-control" name="cpf" value="<?= htmlspecialchars($cpf) ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Data de Nascimento</label>
        <input type="date" class="form-control" name="data_nascimento" value="<?= htmlspecialchars($data_nascimento) ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">E-mail</label>
        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($email) ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Telefone</label>
        <input type="text" class="form-control" name="telefone" value="<?= htmlspecialchars($telefone) ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Turma</label>
        <select class="form-select" name="turma">
            <option value="">— Nenhuma —</option>
            <?php foreach ($turmas as $t): ?>
                <option value="<?= htmlspecialchars($t['id_turma']) ?>" <?= ($id_turma == $t['id_turma']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($t['nome_turma']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div class="form-text">Selecione a turma do aluno (opcional). Se a turma não existir, cadastre-a primeiro.</div>
    </div>

    <button type="submit" class="btn btn-primary">Salvar</button>
    <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
</form>
</div>
</body>
</html>
