<?php
session_start();
$current_user_id = $_SESSION['id_usuario'] ?? null;

if(isset($_GET['id'])){
    $id = $_GET['id'];
    $host = "localhost"; $dbname = "prototipo"; $username = "root"; $password = "";
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$username,$password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("DELETE FROM aluno WHERE id_aluno = :id");
    $stmt->execute([':id'=>$id]);
}
header("Location: index.php");
exit;
?>
