<?php
require(__DIR__ . '/../connect/index.php');

session_start();
$current_user_id = $_SESSION['id_usuario'] ?? null;

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id_usuario = $_GET['id'];

try {
    $stmt = $conn->prepare("DELETE FROM usuario WHERE id_usuario = :id");
    $stmt->execute([':id' => $id_usuario]);

} catch (PDOException $e) {
    echo "Erro ao deletar usuário: " . $e->getMessage();
    exit;
}

header("Location: index.php");
exit;
?>
