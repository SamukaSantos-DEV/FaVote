<?php
require '../php/session_auth.php';
require '../php/config.php';

// Verifica parâmetro id
if (!isset($_GET['id'])) {
    die("ID da eleição não especificado.");
}

$eleicao_id = intval($_GET['id']);

// conecta ao banco (config.php deve definir a classe db)
$db = new db();
$conexao = $db->conecta_mysql();

// RA do aluno logado (use o nome de sessão que você tem: user_ra)
$aluno_ra = $_SESSION['user_ra'] ?? null;
if (!$aluno_ra) {
    header("Location: ../login.php");
    exit;
}

$sql = "
    SELECT 
      e.id,
      e.titulo,
      e.descricao,
      e.data_inicio,
      e.data_fim,
      e.ativa,
      t.id AS turma_id,
      c.nome AS curso_nome,
      s.nome AS semestre_nome
    FROM eleicoes e
    JOIN turmas t ON e.turma_id = t.id
    LEFT JOIN cursos c ON t.curso_id = c.id
    LEFT JOIN semestres s ON t.semestre_id = s.id
    WHERE e.id = ?
    LIMIT 1
";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $eleicao_id);
$stmt->execute();
$result = $stmt->get_result();
$eleicao = $result->fetch_assoc();
$stmt->close();

if (!$eleicao) {
    die("Eleição não encontrada.");
}

// Contar quantos candidatos já se inscreveram nessa eleição
$stmtCount = $conexao->prepare("SELECT COUNT(*) AS total FROM candidatos WHERE eleicao_id = ?");
$stmtCount->bind_param("i", $eleicao_id);
$stmtCount->execute();
$totalCandidatos = $stmtCount->get_result()->fetch_assoc()['total'];
$stmtCount->close();

// Verificar se já é candidato
$stmtCheck = $conexao->prepare("SELECT id FROM candidatos WHERE eleicao_id = ? AND aluno_ra = ? LIMIT 1");
$stmtCheck->bind_param("is", $eleicao_id, $aluno_ra);
$stmtCheck->execute();
$jaCandidato = ($stmtCheck->get_result()->num_rows > 0);
$stmtCheck->close();

// Verificar se eleição está ativa e dentro do período
$dataAgora = new DateTime("now");
$dataInicio = new DateTime($eleicao['data_inicio']);
$dataFim = new DateTime($eleicao['data_fim']);
$eleicaoAberta = ($eleicao['ativa'] == 1) && ($dataAgora >= $dataInicio) && ($dataAgora <= $dataFim);

// Se POST: tentar inserir candidatura
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // impedir se já candidato ou eleição fechada
    if ($jaCandidato) {
        echo "<script>alert('Você já se candidatou para esta eleição.');</script>";
    } elseif (!$eleicaoAberta) {
        echo "<script>alert('Não é possível se candidatar: a eleição não está aberta.');</script>";
    } else {
        $proposta = trim($_POST['proposta'] ?? '');
        if ($proposta === '') {
            echo "<script>alert('Por favor, preencha o campo de proposta.');</script>";
        } else {
            $ins = $conexao->prepare("INSERT INTO candidatos (eleicao_id, aluno_ra, proposta) VALUES (?, ?, ?)");
            $ins->bind_param("iss", $eleicao_id, $aluno_ra, $proposta);
            if ($ins->execute()) {
                $ins->close();
                // opcional: atualizar contagem local
                $totalCandidatos++;
                echo "<script>
                        alert('Candidatura enviada com sucesso!');
                        window.location.href = 'home.php';
                      </script>";
                exit;
            } else {
                echo "<script>alert('Erro ao enviar candidatura. Tente novamente.');</script>";
                $ins->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Candidatar | FaVote</title>
    <link rel="icon" href="../Images/iconlogoFaVote.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap');

        body {
            font-family: 'Poppins';
        }

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(204, 204, 204, 0.3);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background-color: #d6d6d6;
            border-radius: 30px;
            width: 450px;
            padding: 30px 50px 30px 30px;
            box-shadow: 0 0 10px rgb(136, 136, 136);
        }

        .modal-content h2 {
            color: #B60000;
            font-size: 35px;
            text-align: center;
            margin-bottom: 30px;
        }

        .modal-content label {
            display: block;
            font-weight: bold;
            margin-top: 10px;
        }

        .modal-content textarea {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border-radius: 10px;
            border: 2px solid #000;
            font-size: 14px;
            resize: vertical;
        }

        .btn-concluir {
            background-color: #d60e0e;
            color: white;
            border: none;
            padding: 20px;
            width: 105%;
            margin-top: 20px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 22px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-concluir:hover {
            background-color: #b40000;
        }

        .close-btn {
            position: absolute;
            right: 25px;
            top: 25px;
            background-color: #e9e9e9;
            color: #383838;
            border: none;
            padding: 6px 13px;
            border-radius: 10px;
            font-size: 1.5em;
            cursor: pointer;
            transition: background-color 0.6s ease, transform 0.6s ease;
        }

        .close-btn:hover {
            background-color: #eea7a7;
            transform: scale(1.05);
        }
    </style>    
</head>

<body>
    <div class="modal">
        <div class="modal-content animate__animated animate__fadeInUp">
            <form method="post">
                <button type="button" onclick="fechar()" class="close-btn">✖</button>
                <h2>CANDIDATAR</h2>

                <div class="info-box">
                    <p><strong>Eleição:</strong> <?php echo htmlspecialchars($eleicao['titulo']); ?></p>
                    <p><strong>Descrição:</strong> <?php echo htmlspecialchars($eleicao['descricao']); ?></p>
                    <p><strong>Curso:</strong> <?php echo htmlspecialchars($eleicao['curso_nome'] ?? '—'); ?></p>
                    <p><strong>Semestre:</strong> <?php echo htmlspecialchars($eleicao['semestre_nome'] ?? '—'); ?></p>
                    <p><strong>Início:</strong> <?php echo date('d/m/Y H:i', strtotime($eleicao['data_inicio'])); ?></p>
                    <p><strong>Fim:</strong> <?php echo date('d/m/Y H:i', strtotime($eleicao['data_fim'])); ?></p>
                    <p><strong>Candidatos inscritos:</strong> <?php echo intval($totalCandidatos); ?></p>
                    <p><strong>Seu RA:</strong> <?php echo htmlspecialchars($aluno_ra); ?></p>
                </div>

                <?php if (!$eleicaoAberta): ?>
                    <p style="color:#b00; font-weight:700;">A eleição não está aberta para candidaturas.</p>
                <?php elseif ($jaCandidato): ?>
                    <p style="color:green; font-weight:700;">Você já está inscrito como candidato nesta eleição.</p>
                <?php endif; ?>

                <label>Descreva sua proposta:</label>
                <textarea name="proposta" <?php if (!$eleicaoAberta || $jaCandidato)
                    echo 'disabled'; ?>
                    placeholder="Digite aqui sua proposta..."></textarea>

                <button type="submit" class="btn-concluir" <?php if (!$eleicaoAberta || $jaCandidato)
                    echo 'disabled'; ?>>CANDIDATAR</button>
            </form>
        </div>
    </div>

    <script>
        function fechar() { history.back(); }
    </script>
</body>

</html>