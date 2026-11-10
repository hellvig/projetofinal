
<?php
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
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grand Prix | SENAI</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            color: #263445;
        }

        .inicio-header {
            background: white;
            padding: 22px 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e9f0;
        }

        .logo {
            color: #2b4a75;
            font-size: 24px;
            font-weight: bold;
        }

        .logo span {
            color: #4385c5;
        }

        .botoes a {
            text-decoration: none;
            padding: 11px 20px;
            border-radius: 7px;
            margin-left: 10px;
            font-weight: bold;
        }

        .entrar {
            color: #2b4a75;
        }

        .cadastrar {
            background: #2b4a75;
            color: white;
        }

        .hero {
            text-align: center;
            padding: 110px 20px;
        }

        .hero h1 {
            font-size: 42px;
            color: #2b4a75;
            margin-bottom: 20px;
        }

        .hero p {
            max-width: 650px;
            margin: 0 auto 30px;
            color: #64748b;
            line-height: 1.7;
            font-size: 17px;
        }

        .hero a {
            display: inline-block;
            background: #2b4a75;
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-weight: bold;
        }

        .info {
            background: white;
            padding: 35px 20px;
            text-align: center;
        }

        footer {
            text-align: center;
            padding: 20px;
            color: #8995a5;
        }

        @media(max-width: 600px) {
            .inicio-header {
                flex-direction: column;
                gap: 18px;
            }

            .hero h1 {
                font-size: 30px;
            }
        }
    </style>
</head>

<body>

<header class="inicio-header">
    <div class="logo">GRAND <span>PRIX</span></div>

    <div class="botoes">
        <a href="login.php" class="entrar">Entrar</a>
        <a href="cadastro.php" class="cadastrar">Cadastrar</a>
    </div>
</header>

<section class="hero">
    <h1>Bem-vindo ao Grand Prix SENAI!</h1>

    <p>
        Uma plataforma para conectar alunos, professores e unidades,
        organizar demandas, enviar propostas e acompanhar os projetos
        do Grand Prix.
    </p>

    <a href="login.php">Acessar plataforma</a>
</section>

<section class="info">
    <h2>Inovação, colaboração e tecnologia</h2>

    <p>
        Organize suas demandas, compartilhe ideias e acompanhe
        o desenvolvimento dos projetos em um único lugar.
    </p>
</section>

<footer>
    Grand Prix SENAI &copy; <?= date('Y') ?>.
    Todos os direitos reservados.
</footer>

</body>
</html>
