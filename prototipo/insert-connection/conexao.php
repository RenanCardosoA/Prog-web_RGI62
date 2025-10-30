<?php
$host = "localhost";
$dbname = "prototipo";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=localhost;dbname=$dbname", "$username", "$password");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $connstatus = $conn->getAttribute(PDO::ATTR_CONNECTION_STATUS);
        echo $connstatus. "<br>";

    if($conn) {
        echo 'Presença registrada com sucesso!';
    }

    $params = [
        ':nome' => 'Renan Cardoso Aguiar',
        ':turma' => 'RGI62',
        ':data' => date("Y-m-d"),
    ];
    $stmt = $conn->prepare("INSERT INTO presenca(nome, turma, `data`) VALUES (:nome, :turma, `:data`)");
    $result = $stmt->execute($params);


    $conn = null;
} catch (PDOException $e) {
    echo '<pre>';
    print_r($e);
    echo '<hr>';
    print_r($e->errorInfo);
}