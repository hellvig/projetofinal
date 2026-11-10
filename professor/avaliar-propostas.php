
<?php

require_once '../config/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id']) ||
    $_SESSION['tipo_usuario'] !== 'professor') {

    header('Location: ../login.php');
    exit;
}

$mensagem = "";
$erro = "";

// Processamento da avaliação.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $propostaId = filter_input(INPUT_POST, 'proposta_id', FILTER_VALIDATE_INT);
    $status = $_POST['status'] ?? '';
    $feedback = trim($_POST['feedback'] ?? '');

    if (!$propostaId || !in_array($status, ['aprovada', 'rejeitada'], true)) {

        $erro = "Dados de avaliação inválidos.";

    } else {

        try {

            $sql = "UPDATE propostas
                    SET status = ?,
                        feedback = ?,
                        avaliador_id = ?,
                        data_avaliacao = NOW()
                    WHERE id = ?";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                $status,
                $feedback,
                $_SESSION['usuario_id'],
                $propostaId
            ]);

            if ($stmt->rowCount() > 0) {
                $mensagem = "Avaliação registrada com sucesso!";
            } else {
                $erro = "Proposta não encontrada ou sem alterações.";
            }

        } catch (PDOException $e) {
            $erro = "Não foi possível registrar a avaliação.";
        }
    }
}

// Busca das propostas.
try {

    $sql = "SELECT
                p.id,
                p.titulo,
                p.descricao,
                p.status,
                p.data_envio,
                u.nome AS aluno,
                d.titulo AS demanda
            FROM propostas p
            INNER JOIN usuarios u ON p.usuario_id = u.id
            INNER JOIN demandas d ON p.demanda_id = d.id
            ORDER BY p.data_envio DESC";

    $stmt = $pdo->query($sql);
    $propostas = $stmt->fetchAll();

} catch (PDOException $e) {

    $propostas = [];
    $erro = "Não foi possível carregar as propostas. Verifique a estrutura do banco.";

}

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

?>

<style>
    .titulo-pagina {
        color: #2b4a75;
        margin-bottom: 20px;
    }

    .mensagem {
        padding: 13px;
        border-radius: 8px;
        margin-bottom: 18px;
    }

    .sucesso {
        background: #e4f7eb;
        color: #187443;
    }

    .erro {
        background: #ffe8e8;
        color: #b42318;
    }

    .proposta {
        background: white;
        border: 1px solid #e5e9f0;
        padding: 22px;
        border-radius: 12px;
        margin-bottom: 20px;
    }

    .proposta h3 {
        color: #2b4a75;
        margin-bottom: 12px;
    }

    .proposta p {
        color: #64748b;
        margin: 9px 0;
        line-height: 1.5;
    }

    .status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        background: #fff2d9;
        color: #96620a;
        margin: 10px 0;
    }

    .aprovada {
        background: #e4f7eb;
        color: #187443;
    }

    .rejeitada {
        background: #ffe8e8;
        color: #b42318;
    }

    textarea {
        width: 100%;
        min-height: 85px;
        padding: 12px;
        border: 1px solid #dce3ed;
        border-radius: 7px;
        resize: vertical;
        box-sizing: border-box;
        margin: 10px 0;
    }

    .botoes {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    button {
        padding: 11px 18px;
        border: none;
        border-radius: 7px;
        cursor: pointer;
        color: white;
        font-weight: bold;
    }

    .aprovar {
        background: #258653;
    }

    .rejeitar {
        background: #c44b4b;
    }

    .sem-propostas {
        background: white;
        padding: 30px;
        border-radius: 12px;
        text-align: center;
        color: #718096;
    }
</style>

<h2 class="titulo-pagina">Avaliação de propostas</h2>

<p style="color:#718096;margin-bottom:20px">
    Consulte e avalie as propostas enviadas pelos alunos.
</p>

<?php if ($mensagem): ?>
    <div class="mensagem sucesso">
        <?= htmlspecialchars($mensagem) ?>
    </div>
<?php endif; ?>

<?php if ($erro): ?>
    <div class="mensagem erro">
        <?= htmlspecialchars($erro) ?>
    </div>
<?php endif; ?>

<?php if (count($propostas) > 0): ?>

    <?php foreach ($propostas as $proposta): ?>

        <div class="proposta">

            <h3><?= htmlspecialchars($proposta['titulo']) ?></h3>

            <p>
                <strong>Aluno:</strong>
                <?= htmlspecialchars($proposta['aluno']) ?>
            </p>

            <p>
                <strong>Demanda:</strong>
                <?= htmlspecialchars($proposta['demanda']) ?>
            </p>

            <p>
                <strong>Descrição:</strong><br>
                <?= nl2br(htmlspecialchars($proposta['descricao'])) ?>
            </p>

            <p>
                <strong>Data de envio:</strong>
                <?= htmlspecialchars($proposta['data_envio'] ?? '') ?>
            </p>

            <span class="status <?= htmlspecialchars($proposta['status']) ?>">
                <?= htmlspecialchars(ucfirst($proposta['status'])) ?>
            </span>

            <?php if ($proposta['status'] === 'pendente'): ?>

                <form method="POST">

                    <input type="hidden" name="proposta_id"
                           value="<?= (int)$proposta['id'] ?>">

                    <label><strong>Comentário da avaliação:</strong></label>

                    <textarea name="feedback"
                        placeholder="Escreva um comentário sobre a proposta..."></textarea>

                    <div class="botoes">

                        <button type="submit" name="status"
                                value="aprovada" class="aprovar">
                            Aprovar
                        </button>

                        <button type="submit" name="status"
                                value="rejeitada" class="rejeitar">
                            Rejeitar
                        </button>

                    </div>

                </form>

            <?php else: ?>

                <p>
                    <strong>Esta proposta já foi avaliada.</strong>
                </p>

            <?php endif; ?>

        </div>

    <?php endforeach; ?>

<?php else: ?>

    <div class="sem-propostas">
        <h3>Nenhuma proposta encontrada.</h3>
        <p>Quando os alunos enviarem propostas, elas aparecerão aqui.</p>
    </div>

<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
