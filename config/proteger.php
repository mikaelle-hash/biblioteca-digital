<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está conectado
if (!isset($_SESSION["id_usuario"])) {
    header("Location: /biblioteca_digital/login.php");
    exit;
}

// Verifica se o grupo do usuário tem permissão
function exigirGrupo(array $gruposPermitidos): void {

    if (
        !isset($_SESSION["grupo"]) ||
        !in_array($_SESSION["grupo"], $gruposPermitidos, true)
    ) {
        http_response_code(403);
        exit("Acesso negado! Você não tem permissão para acessar esta página.");
    }
}
