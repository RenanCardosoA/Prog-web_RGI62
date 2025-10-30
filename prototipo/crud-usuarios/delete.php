<?php
session_start();
$current_user_id = $_SESSION['id_usuario'] ?? null;

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id_usuario = $_GET['id'];

try {
    $conn = new PDO("mysql:host=localhost;dbname=prototipo;charset=utf8mb4", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("DELETE FROM usuario WHERE id_usuario = :id");
    $stmt->execute([':id' => $id_usuario]);

} catch (PDOException $e) {
    echo "Erro ao deletar usuário: " . $e->getMessage();
    exit;
}

header("Location: index.php");
exit;
?>
