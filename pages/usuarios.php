<?php
require_once "../config/proteger.php";
require_once "../config/conexao.php";

exigirGrupo(["Administradores", "Bibliotecários"]);

$sql = "SELECT u.nome, u.email, g.nome AS grupo, u.ativo
        FROM usuarios u
        INNER JOIN grupos_usuarios g
        ON u.id_grupo = g.id_grupo
        ORDER BY u.nome";
        
$stmt = $conexao->query($sql);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Usuários</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>

<header>
    <h1>Usuários cadastrados</h1>
</header>

<nav>
    <a href="../index.php">Início</a>
</nav>

<main>
    <h2>Lista de usuários</h2>

    <table>
        <tr>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Grupo</th>
            <th>Situação</th>
        </tr>

        <?php foreach ($usuarios as $usuario): ?>
        <tr>
            <td><?= htmlspecialchars($usuario['nome']) ?></td>
            <td><?= htmlspecialchars($usuario['email']) ?></td>
            <td><?= htmlspecialchars($usuario['grupo']) ?></td>
            <td><?= $usuario['ativo'] ? 'Ativo' : 'Inativo' ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</main>

</body>
</html>
