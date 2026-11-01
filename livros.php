<?php
require_once __DIR__ . "/config/proteger.php";
require_once __DIR__ . "/config/conexao.php";

$sql = "SELECT titulo, isbn, editora, ano_publicacao,
        quantidade_total, quantidade_disponivel
        FROM livros
        ORDER BY titulo";

$stmt = $conexao->query($sql);
$livros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Livros cadastrados</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<header>
    <h1>Livros cadastrados</h1>
</header>

<nav>
    <a href="index.php">Início</a>
    <a href="cadastro_livros.php">Cadastrar Livro</a>
</nav>

<main>
    <h2>Lista de livros</h2>

    <table>
        <tr>
            <th>Título</th>
            <th>ISBN</th>
            <th>Editora</th>
            <th>Ano</th>
            <th>Quantidade</th>
            <th>Disponíveis</th>
        </tr>

        <?php foreach ($livros as $livro): ?>
        <tr>
            <td><?= htmlspecialchars($livro['titulo']) ?></td>
            <td><?= htmlspecialchars($livro['isbn']) ?></td>
            <td><?= htmlspecialchars($livro['editora']) ?></td>
            <td><?= htmlspecialchars($livro['ano_publicacao']) ?></td>
            <td><?= htmlspecialchars($livro['quantidade_total']) ?></td>
            <td><?= htmlspecialchars($livro['quantidade_disponivel']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</main>

</body>
</html>
