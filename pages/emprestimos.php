<?php
require_once "../config/proteger.php";
require_once "../config/conexao.php";

if (
    $_SESSION["grupo"] === "Administradores" ||
    $_SESSION["grupo"] === "Bibliotecários"
) {
    $sql = "SELECT * FROM vw_emprestimos_ativos
            ORDER BY data_emprestimo DESC";

    $stmt = $conexao->query($sql);

} else {
    $sql = "SELECT e.id_emprestimo,
                   u.nome AS usuario,
                   e.data_emprestimo,
                   e.data_prevista_devolucao,
                   e.status
            FROM emprestimos e
            INNER JOIN usuarios u
                ON e.id_usuario = u.id_usuario
            WHERE e.id_usuario = ?
              AND e.status = 'ATIVO'
            ORDER BY e.data_emprestimo DESC";

    $stmt = $conexao->prepare($sql);
    $stmt->execute([$_SESSION["id_usuario"]]);
}

$emprestimos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Empréstimos</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>

<header>
    <h1>Empréstimos ativos</h1>
</header>

<nav>
    <a href="../index.php">Início</a>
</nav>

<main>
    <h2>Consulta de empréstimos</h2>

    <table>
        <tr>
            <th>Empréstimo</th>
            <th>Usuário</th>
            <th>Data do empréstimo</th>
            <th>Previsão de devolução</th>
            <th>Status</th>
        </tr>

        <?php foreach ($emprestimos as $emprestimo): ?>
        <tr>
            <td><?= htmlspecialchars($emprestimo['id_emprestimo']) ?></td>
            <td><?= htmlspecialchars($emprestimo['usuario']) ?></td>
            <td><?= htmlspecialchars($emprestimo['data_emprestimo']) ?></td>
            <td><?= htmlspecialchars($emprestimo['data_prevista_devolucao']) ?></td>
            <td><?= htmlspecialchars($emprestimo['status']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</main>

</body>
</html>
