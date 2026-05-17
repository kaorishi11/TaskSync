<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "tarefas";

$conexao = mysqli_connect($host, $username, $password, $database);

if (!$conexao) {
    die("Falha na conexão: " . mysqli_connect_error());
}
?>