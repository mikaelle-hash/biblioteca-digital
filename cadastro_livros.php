<?php
require_once __DIR__ . "/config/proteger.php";
require_once __DIR__ . "/config/conexao.php";

exigirGrupo(["Administradores", "Bibliotecários"]);

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $sql = "INSERT INTO livros
        (id_livro, id_categoria, isbn, titulo, editora,
         ano_publicacao, quantidade_total, quantidade_disponivel)
        VALUES (fn_gerar_id(), ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conexao->prepare($sql);

        $stmt->execute([
            $_POST["id_categoria"],
            $_POST["isbn"],
            $_POST["titulo"],
            $_POST["editora"],
            $_POST["ano_publicacao"],
            $_POST["quantidade_total"],
            $_POST["quantidade_total"]
        ]);

        $mensagem = "Livro cadastrado com sucesso!";

    } catch (PDOException $erro) {
        $mensagem = "Erro ao cadastrar livro: " . $erro->getMessage();
    }
}

$categorias = $conexao->query(
    "SELECT id_categoria, nome FROM categorias ORDER BY nome"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Livros</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<header>
    <h1>Cadastro de Livros</h1>
</header>

<nav>
    <a href="index.php">Início</a>
    <a href="livros.php">Ver livros</a>
</nav>

<main>
    <h2>Novo livro</h2>

    <?php if ($mensagem): ?>
        <p class="sucesso">
            <?= htmlspecialchars($mensagem) ?>
        </p>
    <?php endif; ?>

    <form method="POST">

        <label>Título:</label>
        <input type="text" name="titulo" required>

        <label>ISBN:</label>
        <input type="text" name="isbn" required>

        <label>Editora:</label>
        <input type="text" name="editora">

        <label>Ano de publicação:</label>
        <input type="number" name="ano_publicacao">

        <label>Categoria:</label>
        <select name="id_categoria" required>
            <option value="">Selecione uma categoria</option>

            <?php foreach ($categorias as $categoria): ?>
                <option value="<?= htmlspecialchars($categoria['id_categoria']) ?>">
                    <?= htmlspecialchars($categoria['nome']) ?>
                </option>
            <?php endforeach; ?>

        </select>

        <label>Quantidade:</label>
        <input type="number" name="quantidade_total"
               min="1" required>

        <button type="submit">Cadastrar livro</button>

    </form>
</main>

</body>
</html>
