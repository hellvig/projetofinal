
<?php

require_once '../config/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificação de acesso
if (
    !isset($_SESSION['usuario_id']) ||
    ($_SESSION['tipo_usuario'] ?? '') !== 'professor'
) {
    header('Location: ../login.php');
    exit;
}

// Componentes do sistema
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Indicadores
$totalPropostas = 0;
$propostasPendentes = 0;
$propostasAprovadas = 0;
$propostasRejeitadas = 0;
$totalDemandas = 0;

$erro = '';

try {

    // Total de propostas
    $stmt = $pdo->query(
        "SELECT COUNT(*) FROM propostas"
    );
    $totalPropostas = $stmt->fetchColumn();

    // Propostas pendentes
    $stmt = $pdo->query(
        "SELECT COUNT(*) FROM propostas WHERE status = 'pendente'"
    );
    $propostasPendentes = $stmt->fetchColumn();

    // Propostas aprovadas
    $stmt = $pdo->query(
        "SELECT COUNT(*) FROM propostas WHERE status = 'aprovada'"
    );
    $propostasAprovadas = $stmt->fetchColumn();

    // Propostas rejeitadas
    $stmt = $pdo->query(
        "SELECT COUNT(*) FROM propostas WHERE status = 'rejeitada'"
    );
    $propostasRejeitadas = $stmt->fetchColumn();

    // Total de demandas
    $stmt = $pdo->query(
        "SELECT COUNT(*) FROM demandas"
    );
    $totalDemandas = $stmt->fetchColumn();

} catch (PDOException $e) {

    $erro = "Não foi possível carregar os indicadores.";

}

?>

<link rel="stylesheet" href="../css/dashboard.css">

<style>

    .titulo-dashboard {
        color: #2b4a75;
        margin-bottom: 8px;
    }

    .subtitulo-dashboard {
        color: #718096;
        margin-bottom: 25px;
    }

    .indicadores {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 25px;
    }

    .card-indicador {
        background: #ffffff;
        padding: 23px;
        border-radius: 12px;
        border: 1px solid #e5e9f0;
        transition: 0.2s;
    }

    .card-indicador:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .card-indicador h3 {
        color: #718096;
        font-size: 14px;
        margin-bottom: 14px;
    }

    .card-indicador strong {
        color: #2b4a75;
        font-size: 30px;
    }

    .boas-vindas {
        background: #ffffff;
        border: 1px solid #e5e9f0;
        padding: 28px;
        border-radius: 12px;
        margin-bottom: 25px;
    }

    .boas-vindas h2 {
        color: #2b4a75;
        margin-bottom: 12px;
    }

    .boas-vindas p {
        color: #64748b;
        line-height: 1.7;
    }

    .acoes {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-top: 22px;
    }

    .botao {
        display: inline-block;
        padding: 13px 20px;
        background: #2b4a75;
        color: white;
        text-decoration: none;
        border-radius: 7px;
        font-weight: bold;
        transition: 0.2s;
    }

    .botao:hover {
        background: #4385c5;
    }

    .botao-secundario {
        background: #eaf2fb;
        color: #2b4a75;
    }

    .botao-secundario:hover {
        background: #dceafa;
    }

    .mensagem-erro {
        background: #ffe8e8;
        color: #b42318;
        padding: 14px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    @media (max-width: 1000px) {
        .indicadores {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 550px) {
        .indicadores {
            grid-template-columns: 1fr;
        }

        .acoes {
            flex-direction: column;
        }

        .botao {
            text-align: center;
        }
    }

</style>

<!-- Título -->
<h1 class="titulo-dashboard">
    Painel do professor
</h1>

<p class="subtitulo-dashboard">
    Bem-vindo(a), <?= htmlspecialchars($_SESSION['nome']) ?>!
    Acompanhe as atividades do Grand Prix SENAI.
</p>

<!-- Mensagem de erro -->
<?php if ($erro): ?>

    <div class="mensagem-erro">
        <?= htmlspecialchars($erro) ?>
    </div>

<?php endif; ?>

<!-- Indicadores -->
<div class="indicadores">

    <div class="card-indicador">
        <h3>Total de propostas</h3>
        <strong><?= (int)$totalPropostas ?></strong>
    </div>

    <div class="card-indicador">
        <h3>Propostas pendentes</h3>
        <strong><?= (int)$propostasPendentes ?></strong>
    </div>

    <div class="card-indicador">
        <h3>Propostas aprovadas</h3>
        <strong><?= (int)$propostasAprovadas ?></strong>
    </div>

    <div class="card-indicador">
        <h3>Propostas rejeitadas</h3>
        <strong><?= (int)$propostasRejeitadas ?></strong>
    </div>

</div>

<!-- Boas-vindas -->
<div class="boas-vindas">

    <h2>Área do professor</h2>

    <p>
        Nesta área você pode acompanhar as propostas enviadas
        pelos alunos, consultar as demandas cadastradas e
        realizar avaliações dos projetos participantes
        do Grand Prix SENAI.
    </p>

    <p>
        Atualmente existem
        <strong><?= (int)$totalDemandas ?></strong>
        demandas cadastradas na plataforma.
    </p>

    <div class="acoes">

        <a href="avaliar-propostas.php" class="botao">
            Avaliar propostas
        </a>

        <a href="../pages/demandas.php"
           class="botao botao-secundario">
            Consultar demandas
        </a>

    </div>

</div>

<?php

require_once '../includes/footer.php';

?>
