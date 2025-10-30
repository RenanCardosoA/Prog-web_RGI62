<?php
$nome = "";
$turma = "";
$data_presenca = "";

$errorMessage = "";
$sucessMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['name'];
    $turma = $_POST['turma'];
    $data_presenca = $_POST['data_presenca'];

    do {
        if (empty($nome) || empty($turma) || empty($data_presenca)) {
            $errorMessage = "Todos os campos são obrigatórios.";
            break;
        }

        $nome = "";
        $turma = "";
        $data_presenca = "";

        $sucessMessage = "Presença registrada com sucesso.";

    } while (false);
    
    $host = "localhost"; 
    $dbname = "prototipo_php";
    $username = "root";
    $password = "1234";

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo '<pre>';
        print_r($e);
        echo '<hr>';
        print_r($e->errorInfo);
    }

    $params = [
        ':nome' => $nome,
        ':turma' => $turma,
        ':data_presenca' => $data_presenca,
    ];
    $stmt = $conn->prepare("INSERT INTO presenca(nome, turma, data_presenca) VALUES (:nome, :turma, :data_presenca)");
    $result = $stmt->execute($params);

    if ($result) {
        header('Location: /prototipo/crud/index.php');
    } else {
        echo "Erro ao registrar presença.";
    }

    $conn = null;
} 
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área administrativa - Presença</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h2>Criar presença</h2>

        <?php
        if (!empty($errorMessage)) {
            echo "
            <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                <strong>$errorMessage</strong>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
            ";
        }
        ?>

        <form method="post">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Nome</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="name" value="<?php echo $nome; ?>">
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Turma</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="turma" value="<?php echo $turma; ?>">
                </div>
        </div>

    <div class="row mb-3">
        <label class="col-sm-3 col-form-label">Data</label>
        <div class="col-sm-9">
            <input type="date" class="form-control" name="data_presenca" value="<?php echo $data_presenca; ?>">
        </div>
    </div>
            

            <?php
            if (!empty($sucessMessage)) {
                echo "
                <div class='row mb-3'>
                    <div class='offset-sm-3 col-sm-6'>
                        <div class='alert alert-success alert-dismissible fade show' role='alert'>
                            <strong>$sucessMessage</strong>
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                        </div>
                    </div>
                </div>
                ";
            }
            ?>
            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
                <div class="col-sm-3 d-grid">
                    <a class="btn btn-outline-primary" href="/prototipo/crud/index.php" role="button">Cancelar</a>   
                </div>
            </div>
        </form>
    </div>
</body>
</html>