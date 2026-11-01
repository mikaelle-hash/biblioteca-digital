<?php
session_start();

require_once "config/conexao.php";

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $senha = $_POST["senha"];

    $sql = "SELECT u.id_usuario, u.nome, u.email,
                   u.senha_hash, g.nome AS grupo
            FROM usuarios u
            INNER JOIN grupos_usuarios g
            ON u.id_grupo = g.id_grupo
            WHERE u.email = ? AND u.ativo = 1";

    $stmt = $conexao->prepare($sql);
    $stmt->execute([$email]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    $senhaCorreta = false;

if ($usuario) {
    $senhaCorreta = password_verify($senha, $usuario["senha_hash"]);

    if (!$senhaCorreta && hash_equals($usuario["senha_hash"], hash("sha256", $senha))) {
        $senhaCorreta = true;

        $novoHash = password_hash($senha, PASSWORD_DEFAULT);

        $atualizar = $conexao->prepare(
            "UPDATE usuarios SET senha_hash = ? WHERE id_usuario = ?"
        );

        $atualizar->execute([$novoHash, $usuario["id_usuario"]]);
    }
}

if ($senhaCorreta) {

        session_regenerate_id(true);

        $_SESSION["id_usuario"] = $usuario["id_usuario"];
        $_SESSION["nome"] = $usuario["nome"];
        $_SESSION["email"] = $usuario["email"];
        $_SESSION["grupo"] = $usuario["grupo"];

        header("Location: index.php");
        exit;

    } else {
        $mensagem = "E-mail ou senha inválidos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Biblioteca Digital</title>

    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

<header>
    <h1>Biblioteca Digital</h1>
    <p>Acesse sua conta</p>
</header>

<main>

    <h2>Entrar no sistema</h2>

    <?php if ($mensagem): ?>
        <p class="erro">
            <?= htmlspecialchars($mensagem) ?>
        </p>
    <?php endif; ?>

    <form method="POST">

        <label>E-mail:</label>

        <input
            type="email"
            name="email"
            required
        >

        <label>Senha:</label>

        <input
            type="password"
            name="senha"
            required
        >

        <button type="submit">
            Entrar
        </button>

    </form>

</main>

<footer>
    <p>Biblioteca Digital - Projeto Acadêmico</p>
</footer>

</body>
</html>
