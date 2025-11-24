<?php
$host = "localhost";
$dbname = "sistema_presenca"; 
$username = "root";
$password = "1234";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Conectado com sucesso!<br>";

    $id_aluno = 1; 
    $id_turma = 1; 
    $data = date("Y-m-d");
    $status = "presente";
    $observacao = "Presença adicionada através de qr code.";


    $sql = "INSERT INTO presenca (id_aluno, id_turma, data_presenca, status, observacao)
            VALUES (:id_aluno, :id_turma, :data_presenca, :status, :observacao)";


    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':id_aluno' => $id_aluno,
        ':id_turma' => $id_turma,
        ':data_presenca' => $data,
        ':status' => $status,
        ':observacao' => $observacao
    ]);

    echo "Presença registrada com sucesso!";
} catch (PDOException $e) {
    echo "Erro ao conectar/inserir no banco: " . $e->getMessage();
}
