<?php
require(__DIR__ . '/../connect/index.php');

session_start();
$current_user_id = $_SESSION['id_usuario'] ?? null;

if(isset($_GET['id'])){
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM presenca WHERE id_presenca=:id");
    $stmt->execute([':id'=>$id]);
}
header("Location: index.php");
exit;
?>
