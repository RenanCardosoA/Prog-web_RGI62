<?php
session_start();
$current_user_id = $_SESSION['id_usuario'] ?? null;

$id = $_GET['id'] ?? null;
if(!$id) header("Location: index.php");

require(__DIR__ . '/../connect/index.php');

$errorMessage = $successMessage = "";

// Buscar dados existentes
$stmt = $conn->prepare("SELECT * FROM aluno WHERE id_aluno = :id");
$stmt->execute([':id' => $id]);
$aluno = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$aluno) header("Location: index.php");

$nome = $aluno['nome'];
$matricula = $aluno['matricula'];
$cpf = $aluno['cpf'];
$data_nascimento = $aluno['data_nascimento'];
$email = $aluno['email'];
$telefone = $aluno['telefone'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $matricula = trim($_POST['matricula']);
    $cpf = trim($_POST['cpf']);
    $data_nascimento = trim($_POST['data_nascimento']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);

    if(empty($nome) || empty($matricula)){
        $errorMessage = "Nome e matrícula são obrigatórios.";
    } else {
        $stmt = $conn->prepare("
            UPDATE aluno SET nome=:nome, matricula=:matricula, cpf=:cpf, 
            data_nascimento=:data_nascimento, email=:email, telefone=:telefone
            WHERE id_aluno=:id
        ");
        $stmt->execute([
            ':nome'=>$nome,
            ':matricula'=>$matricula,
            ':cpf'=>$cpf,
            ':data_nascimento'=>$data_nascimento,
            ':email'=>$email,
            ':telefone'=>$telefone,
            ':id'=>$id
        ]);
        $successMessage = "Aluno atualizado com sucesso.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Editar Aluno</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
<link rel="shortcut icon" href="../img/ico/favicon.ico" type="image/x-icon">
</head>
<body>
<div class="container my-5">
<h2>Editar Aluno</h2>

<?php if($errorMessage): ?>
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong><?= $errorMessage ?></strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if($successMessage): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong><?= $successMessage ?></strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<form method="post">
    <div class="mb-3">
        <label class="form-label">Nome</label>
        <input type="text" class="form-control" name="nome" value="<?= htmlspecialchars($nome) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Matrícula</label>
        <input type="text" class="form-control" name="matricula" value="<?= htmlspecialchars($matricula) ?>">
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
        <label class="form-label">Email</label>
        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($email) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Telefone</label>
        <input type="text" class="form-control" name="telefone" value="<?= htmlspecialchars($telefone) ?>">
    </div>

    <button type="submit" class="btn btn-success">Salvar</button>
    <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
</form>
</div>
</body>
</html>
