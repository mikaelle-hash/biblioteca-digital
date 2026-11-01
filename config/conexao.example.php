<?php

$host = "localhost";
$banco = "biblioteca_digital";
$usuario = "SEU_USUARIO_MYSQL";
$senha = "SUA_SENHA_MYSQL";

try {
    $conexao = new PDO(
        "mysql:host=$host;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senha
    );

    $conexao->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $erro) {
    die("Erro na conexão com o banco: " . $erro->getMessage());
}

?>