<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: gerenciamento.php");
    exit();
}

$id = $_GET['id'];
$idUsuario = $_SESSION['usuario_id'];

$stmt = $conexao->prepare("
    DELETE FROM tarefas
    WHERE id_tarefa = ?
    AND id_usuario = ?
");

$stmt->bind_param("ii", $id, $idUsuario);
$stmt->execute();

header("Location: gerenciamento.php");
exit();
?>