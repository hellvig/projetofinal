
<?php

require_once 'config/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['usuario_id'])) {
    $tipo = $_SESSION['tipo_usuario'] ?? 'aluno';

    if ($tipo === 'admin') {
        header('Location: admin/dashboard.php');
    } elseif ($tipo === 'professor') {
        header('Location: professor/dashboard.php');
    } else {
        header('Location: pages/demandas.php');
    }
    exit;
}

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === "" || $senha === "") {
        $erro = "Preencha seu e-mail e sua senha.";

    } else {

        try {

            $sql = "SELECT id, nome, email, senha, tipo_usuario
                    FROM usuarios
                    WHERE email = ?";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([$email]);

            $usuario = $stmt->fetch();

            if ($usuario && password_verify($senha, $usuario['senha'])) {

                session_regenerate_id(true);

                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nome'] = $usuario['nome'];
                $_SESSION['email'] = $usuario['email'];
                $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

                if ($usuario['tipo_usuario'] === 'admin') {
                    header('Location: admin/dashboard.php');

                } elseif ($usuario['tipo_usuario'] === 'professor') {
                    header('Location: professor/dashboard.php');

                } else {
                    header('Location: pages/demandas.php');
                }

                exit;

            } else {
                $erro = "E-mail ou senha incorretos.";
            }

        } catch (PDOException $e) {
            $erro = "Não foi possível realizar o login.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Grand Prix</title>
    <link rel="stylesheet" href="css/login.css">

    <style>
        body {
            background: #f4f7fb;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .login {
            background: white;
            width: 100%;
            max-width: 400px;
            padding: 35px;
            border-radius: 14px;
            box-shadow: 0 5px 25px rgba(0,0,0,.07);
        }

        h1 {
            color: #2b4a75;
            text-align: center;
        }

        input {
            width: 100%;
            padding: 13px;
            margin: 10px 0 20px;
            border: 1px solid #dce3ed;
            border-radius: 7px;
            box-sizing: border-box;
        }

        label {
            color: #526174;
        }

        button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 7px;
            background: #2b4a75;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .erro {
            background: #ffe8e8;
            color: #b42318;
            padding: 12px;
            border-radius: 7px;
            margin-bottom: 15px;
        }

        .links {
            text-align: center;
            margin-top: 20px;
        }

        .links a {
            color: #2b4a75;
            text-decoration: none;
        }
    </style>
</head>

<body>

<div class="login">

    <h1>GRAND PRIX</h1>

    <p style="text-align:center;color:#718096">
        Acesse sua conta
    </p>

    <?php if ($erro): ?>
        <div class="erro">
            <?= htmlspecialchars($erro) ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label>E-mail</label>
        <input type="email" name="email" required
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

        <label>Senha</label>
        <input type="password" name="senha" required>

        <button type="submit">Entrar</button>

    </form>

    <div class="links">
        <p>Não possui conta?</p>
        <a href="cadastro.php">Cadastre-se</a>
        <br><br>
        <a href="index.php">Voltar ao início</a>
    </div>

</div>

</body>
</html>
