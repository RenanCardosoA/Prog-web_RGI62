<?php
session_start();
$current_user_id = $_SESSION['id_usuario'] ?? null;

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn = new PDO("mysql:host=localhost;dbname=sistema_presenca;charset=utf8mb4", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("DELETE FROM turma WHERE id_turma = :id");
    $stmt->execute([':id' => $id]);
}

header("Location: index.php");
exit;
?>
