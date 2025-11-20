<?php require '../php/session_auth.php'; ?>
<?php

include '../php/config.php';

$query_finalizar = $conexao->prepare("UPDATE eleicoes SET ativa = 0 WHERE ativa = 1 AND data_fim <= NOW()");
$query_finalizar->execute();
$query_finalizar->close();
$query_finalizar = $conexao->prepare("UPDATE eleicoes SET ativa = 1 WHERE ativa = 0 AND data_fim >= NOW()");
$query_finalizar->execute();
$query_finalizar->close();

// Eleições ativas com informações de curso e semestre
$sqlEleicoesAtivas = "
    SELECT 
        e.*,
        t.id AS turma_id,
        c.nome AS curso_nome,
        s.nome AS semestre_nome
    FROM eleicoes e
    LEFT JOIN turmas t ON e.turma_id = t.id
    LEFT JOIN cursos c ON t.curso_id = c.id
    LEFT JOIN semestres s ON t.semestre_id = s.id
    WHERE e.ativa != 0
    ORDER BY e.dataPostagem DESC
";
$resultAtivas = $conexao->query($sqlEleicoesAtivas);

// Eleições passadas com informações de curso e semestre
$sqlEleicoesPassadas = "
    SELECT 
        e.*,
        t.id AS turma_id,
        c.nome AS curso_nome,
        s.nome AS semestre_nome
    FROM eleicoes e
    LEFT JOIN turmas t ON e.turma_id = t.id
    LEFT JOIN cursos c ON t.curso_id = c.id
    LEFT JOIN semestres s ON t.semestre_id = s.id
    WHERE e.ativa = 0
    ORDER BY e.dataPostagem DESC
";
$resultPassadas = $conexao->query($sqlEleicoesPassadas);


$sqlNoticias = "SELECT * FROM noticias ORDER BY dataPublicacao DESC";
$resultNoticias = $conexao->query($sqlNoticias);

$sqlUsuarios = "
    SELECT 
        a.id AS aluno_id,
        a.ra,
        a.nome,
        a.email_institucional,
        c.nome AS curso_nome,
        s.nome AS semestre_nome
    FROM alunos a
    LEFT JOIN turmas t ON a.turma_id = t.id
    LEFT JOIN cursos c ON t.curso_id = c.id
    LEFT JOIN semestres s ON t.semestre_id = s.id
    LIMIT 15
";

$resultUsuarios = $conexao->query($sqlUsuarios);

$sqlTurmas = "
    SELECT 
        t.id AS turma_id,
        c.id AS curso_id,
        c.nome AS curso_nome,
        s.nome AS semestre_nome,
        COUNT(a.id) AS qtd_alunos
    FROM turmas t
    LEFT JOIN cursos c ON t.curso_id = c.id
    LEFT JOIN semestres s ON t.semestre_id = s.id
    LEFT JOIN alunos a ON a.turma_id = t.id
    GROUP BY t.id
    LIMIT 7
";

$resultTurmas = $conexao->query($sqlTurmas);

// EXCLUIR ELEIÇÃO (quando vem com ?id=)
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conexao->prepare("
        DELETE FROM votos 
        WHERE candidato_id IN (SELECT id FROM candidatos WHERE eleicao_id = ?)
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conexao->prepare("DELETE FROM candidatos WHERE eleicao_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conexao->prepare("DELETE FROM eleicoes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $ok = $stmt->execute();
    $stmt->close();

    if ($ok) {
        echo "<script>alert('Eleição excluída com sucesso!'); window.location.href='dashboard.php';</script>";
        exit;
    } else {
        echo "<script>alert('Erro ao excluir eleição.'); window.location.href='dashboard.php';</script>";
        exit;
    }
}

// BUSCA CURSOS
$cursos = $conexao->query("SELECT * FROM cursos ORDER BY nome")->fetch_all(MYSQLI_ASSOC);

// BUSCA SEMESTRES
$semestres = $conexao->query("SELECT * FROM semestres ORDER BY id")->fetch_all(MYSQLI_ASSOC);

// BUSCA TURMAS
$turmas = $conexao->query("SELECT * FROM turmas")->fetch_all(MYSQLI_ASSOC);

// BUSCA ALUNOS
$alunos = $conexao->query("SELECT ra, nome, turma_id FROM alunos ORDER BY nome")->fetch_all(MYSQLI_ASSOC);


if (isset($_POST['criar_eleicao'])) {

    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];

    // COMBINAR DATA + HORA
    $data_inicio = $_POST['data_inicio_data'] . " " . $_POST['data_inicio_hora'];
    $data_fim = $_POST['data_fim_data'] . " " . $_POST['data_fim_hora'];

    $curso_id = $_POST['curso_id'];
    $semestre_id = $_POST['semestre_id'];

    // ENCONTRAR TURMA
    $sqlTurma = $conexao->prepare("SELECT id FROM turmas WHERE curso_id = ? AND semestre_id = ?");
    $sqlTurma->bind_param("ii", $curso_id, $semestre_id);
    $sqlTurma->execute();
    $turma = $sqlTurma->get_result()->fetch_assoc();

    if (!$turma) {
        echo "<script>alert('Nenhuma turma encontrada para este curso e semestre');</script>";
        exit;
    }

    $turma_id = $turma['id'];

    // CRIAR ELEIÇÃO
    $sql = $conexao->prepare("
        INSERT INTO eleicoes (titulo, descricao, data_inicio, data_fim, turma_id)
        VALUES (?, ?, ?, ?, ?)
    ");
    $sql->bind_param("ssssi", $titulo, $descricao, $data_inicio, $data_fim, $turma_id);
    $sql->execute();

    $eleicao_id = $conexao->insert_id;

    // INSERIR TODOS OS ALUNOS DA TURMA
    $sqlAlunos = $conexao->prepare("SELECT ra FROM alunos WHERE turma_id = ?");
    $sqlAlunos->bind_param("i", $turma_id);
    $sqlAlunos->execute();
    $resultAlunos = $sqlAlunos->get_result();

    while ($aluno = $resultAlunos->fetch_assoc()) {
        $ra = $aluno['ra'];
        $conexao->query("INSERT INTO candidatos (eleicao_id, aluno_ra) VALUES ($eleicao_id, '$ra')");
    }

    header("Location: dashboard.php?success=eleicao_criada");
    exit;
}

if (isset($_POST['editar_eleicao'])) {

    $id = intval($_POST['id']);
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];

    $sql = $conexao->prepare("
        UPDATE eleicoes 
        SET titulo = ?, descricao = ?, data_inicio = ?, data_fim = ?
        WHERE id = ?
    ");
    $sql->bind_param("ssssi", $titulo, $descricao, $data_inicio, $data_fim, $id);

    if ($sql->execute()) {
        echo "<script>alert('Eleição editada com sucesso!'); window.location='dashboard.php';</script>";
        exit;
    } else {
        echo "<script>alert('Erro ao editar eleição!');</script>";
    }
}

// ==========================
// RETORNAR DADOS + CANDIDATOS
// ==========================
if (isset($_GET['get_eleicao'])) {

    $id = intval($_GET['get_eleicao']);

    // BUSCA DA ELEIÇÃO + TURMA + CURSO + SEMESTRE
    $sql = $conexao->prepare("
        SELECT 
            e.*, 
            c.nome AS curso_nome,
            s.nome AS semestre_nome
        FROM eleicoes e
        LEFT JOIN turmas t ON e.turma_id = t.id
        LEFT JOIN cursos c ON t.curso_id = c.id
        LEFT JOIN semestres s ON t.semestre_id = s.id
        WHERE e.id = ?
    ");
    $sql->bind_param("i", $id);
    $sql->execute();
    $eleicao = $sql->get_result()->fetch_assoc();

    if (!$eleicao) {
        echo json_encode(["erro" => "Eleição não encontrada"]);
        exit;
    }

    // BUSCA DOS CANDIDATOS
    $sqlCand = $conexao->prepare("
        SELECT 
            c.id AS candidato_id,
            a.nome,
            a.ra
        FROM candidatos c
        LEFT JOIN alunos a ON a.ra = c.aluno_ra
        WHERE c.eleicao_id = ?
    ");
    $sqlCand->bind_param("i", $id);
    $sqlCand->execute();
    $candResult = $sqlCand->get_result();

    $candidatos = [];
    while ($c = $candResult->fetch_assoc()) {
        $candidatos[] = $c;
    }

    // RETORNO FINAL
    echo json_encode([
        "eleicao" => $eleicao,
        "candidatos" => $candidatos
    ]);

    exit;
}



?>
<?php if (isset($_GET['success']) && $_GET['success'] === 'eleicao_criada'): ?>
    <script>
        alert("Eleição criada com sucesso!");

        // Remove o parâmetro da URL sem recarregar a página
        if (history.pushState) {
            const novaURL = window.location.href.split("?")[0];
            window.history.replaceState({}, document.title, novaURL);
        }
    </script>
<?php endif; ?>

<?php
if (isset($_GET['success']) && $_GET['success'] === 'noticia_excluida') {
    echo "<script>alert('Notícia excluída com sucesso!');</script>";
}

if (isset($_GET['error'])) {
    echo "<script>alert('Erro ao excluir a notícia.');</script>";
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard | FaVote</title>
    <link rel="stylesheet" href="../Styles/dashboard.css">
    <link rel="icon" href="../Images/iconlogoFaVote.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


</head>


<body>



    <header class="header">
        <div class="logo"><img src="../Images/logofatec.png" width="190"></div>


        <nav class="nav">
            <a href="home.php">Home</a>
            <a href="eleAtive.php">Eleições Ativas</a>
            <a href="news.php">Notícias</a>
            <a href="elePassa.php">Eleições Passadas</a>
            <a href="dashboard.php"
                style="background-color: white; color: brown; padding: 4px 8px; border-radius: 4px; text-decoration: none; transition: background-color 0.6s ease;"
                onmouseover="this.style.backgroundColor='#ccc'" onmouseout="this.style.backgroundColor='white'">
                DASHBOARD
            </a>
        </nav>
        <div class="user-icon">
            <img src="../Images/user.png" width="50" alt="user" />
            <div class="user-popup">
                <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>
                <p>FATEC “Dr. Ogari de Castro Pacheco”</p>
                <?php

                // Supondo que o login salva o e-mail na sessão assim:
                $emailLogado = $_SESSION['user_email'] ?? null;
                ?>

                <!-- ... resto do seu HTML ... -->

                <?php if ($emailLogado !== 'admin@fatec.sp.gov.br'): ?>
                    <strong>
                        <p><?php echo htmlspecialchars($_SESSION['curso_nome']); ?></p>
                    </strong>
                    <p><?php echo htmlspecialchars($_SESSION['semestre_nome']); ?></p>
                <?php endif; ?>
                <div class="sair">
                    <a href="../php/logout.php">Sair<i style="margin-left: 5px;"
                            class="fa-solid fa-right-from-bracket"></i></a>
                </div>
            </div>
        </div>
    </header>




    <main class="main-content">
        <div class="section-header">
            <h1>Eleições</h1>
            <button id="criarMais" class="create-btn">Criar Nova +</button>
        </div>
        <div class="elections-active">
            <?php if ($resultAtivas && $resultAtivas->num_rows > 0): ?>
                <?php foreach ($resultAtivas as $row): ?>
                    <div class="election-card">
                        <div class="election-id">
                            <?php
                            $semestre = $row['semestre_nome'] ?? '-';
                            $semestre = preg_replace('/[^0-9]/', '', $semestre);
                            ?>
                            <span><?= htmlspecialchars($semestre) ?></span>
                            <span><?= htmlspecialchars($row['curso_nome'] ?? '-') ?></span>
                        </div>
                        <div class="election-details">
                            <div class="election-info">
                                <div class="election-title">
                                    <?= htmlspecialchars($row['titulo']) ?>
                                </div>
                                <div class="election-description">
                                    <?= htmlspecialchars($row['descricao']) ?>
                                </div>
                            </div>

                            <div class="election-dates">
                                <div class="election-date">
                                    Publicada em: <?= date('d/m/Y H:i', strtotime($row['dataPostagem'])) ?>
                                </div>
                                <div class="election-date">
                                    Começou em: <?= date('d/m/Y H:i', strtotime($row['data_inicio'])) ?>
                                </div>
                                <div class="election-date">
                                    Termina em: <?= date('d/m/Y H:i', strtotime($row['data_fim'])) ?>
                                </div>
                            </div>

                            <div class="election-actions">
                                <button type="button" onclick="abrirModalEditar(<?= $row['id'] ?>)" class="btn-edit">
                                    <i class="fas fa-edit" style="margin-right: 5px;"></i> EDITAR
                                </button>


                                <a href="dashboard.php?id=<?= $row['id'] ?>" class="btn-cancel"
                                    id="btnCancelar<?= $row['id'] ?>"
                                    onclick="return confirm('AVISO: Você está prestes a cancelar/excluir “<?= htmlspecialchars($row['titulo']) ?>”, criada em <?= date('d/m/Y H:i', strtotime($row['dataPostagem'])) ?>')">
                                    <i class="fas fa-times" style="margin-right: 5px;"></i> Excluir
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php else: ?>
                <p>Nenhuma eleição ativa encontrada.</p>
            <?php endif; ?>
        </div>


        <h2 class="section-title">Eleições Passadas</h2>
        <div class="elections-past">
            <?php if ($resultPassadas && $resultPassadas->num_rows > 0): ?>
                <?php foreach ($resultPassadas as $row): ?>
                    <div class="election-card election-past">
                        <div class="election-id">
                            <?php
                            $semestre = $row['semestre_nome'] ?? '-';
                            $semestre = preg_replace('/[^0-9]/', '', $semestre);
                            ?>
                            <span><?= htmlspecialchars($semestre) ?></span>
                            <span><?= htmlspecialchars($row['curso_nome'] ?? '-') ?></span>
                        </div>

                        <div class="election-details">
                            <div class="election-info">
                                <div class="election-title">
                                    <?= htmlspecialchars($row['titulo']) ?>
                                </div>
                                <div class="election-description">
                                    <?= htmlspecialchars($row['descricao']) ?>
                                </div>
                            </div>

                            <div class="election-dates">
                                <div class="election-date">
                                    Publicada em: <?= date('d/m/Y H:i', strtotime($row['dataPostagem'])) ?>
                                </div>
                                <div class="election-date">
                                    Começou em: <?= date('d/m/Y H:i', strtotime($row['data_inicio'])) ?>
                                </div>
                                <div class="election-date">
                                    Termina em: <?= date('d/m/Y H:i', strtotime($row['data_fim'])) ?>
                                </div>
                            </div>

                            <div class="election-actions">

                                <button onclick="gerarAtaPDF(<?= $row['id'] ?>)" class="btn-pdf" id="btnPdf<?= $row['id'] ?>">


                                    <i class="fa-solid fa-download" style="margin-right: 5px;"></i>
                                    Ata Oficial
                                </button>                               

                                <a href="dashboard.php?id=<?= $row['id'] ?>" class="btn-delete" id="btnExcluir<?= $row['id'] ?>"
                                    onclick="return confirm('AVISO: Você está prestes a cancelar/excluir “<?= htmlspecialchars($row['titulo']) ?>”, criada em <?= date('d/m/Y H:i', strtotime($row['dataPostagem'])) ?>')">
                                    <i class="fas fa-trash" style="margin-right: 5px;"></i> Excluir
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php else: ?>
                <p>Nenhuma eleição passada encontrada.</p>
            <?php endif; ?>
        </div>


        <div class="news-section">
            <div class="news-header">
                <h2>Notícias</h2>
                <a href="criarNoticia.php" id="criarMais2" class="create-btn">Criar Nova +</a>
            </div>

            <div class="news-cards">
                <?php if ($resultNoticias && $resultNoticias->num_rows > 0): ?>
                    <?php foreach ($resultNoticias as $noticia): ?>
                        <div class="news-card">
                            <div class="card-content">
                                <h3><?= htmlspecialchars($noticia['titulo']) ?></h3>
                                <p><?= nl2br(htmlspecialchars($noticia['descricao'])) ?></p>
                                <p class="publication-date">
                                    Publicado em: <?= date('d/m/Y H:i', strtotime($noticia['dataPublicacao'])) ?>
                                </p>
                            </div>

                            <div class="card-actions">
                                <a href="../php/excluir_noticia.php?id=<?= $noticia['id'] ?>" class="delete-btn"
                                    onclick="return confirm('AVISO: Você está prestes a excluir a notícia “<?= htmlspecialchars($noticia['titulo']) ?>”, criada em <?= date('d/m/Y H:i', strtotime($noticia['dataPublicacao'])) ?>')">
                                    EXCLUIR
                                </a>

                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Nenhuma notícia publicada ainda.</p>
                <?php endif; ?>
            </div>

            <?php if ($resultNoticias && $resultNoticias->num_rows > 0): ?>

            <?php endif; ?>
        </div>


        <div class="container" style="margin-top: 5%;">
            <div class="table-header">
                <h2>Alunos</h2>
                <a href="alunosDash.php" class="ver-todos-btn">
                    Editar 🖉 ➜
                </a>

            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>RA</th>
                            <th>Nome</th>
                            <th>E-Mail</th>
                            <th>Curso</th>
                            <th>Período</th>
                        </tr>
                    </thead>
                    <tbody id="usuarios-tbody">
                        <?php
                        if ($resultUsuarios && $resultUsuarios->num_rows > 0) {
                            while ($usuario = $resultUsuarios->fetch_assoc()) {
                                echo '<tr>';
                                echo '  <td>' . htmlspecialchars($usuario['ra']) . '</td>';
                                echo '  <td>' . htmlspecialchars($usuario['nome']) . '</td>';
                                echo '  <td>' . htmlspecialchars($usuario['email_institucional']) . '</td>';
                                echo '  <td>' . htmlspecialchars($usuario['curso_nome'] ?? '-') . '</td>';
                                echo '  <td>' . htmlspecialchars($usuario['semestre_nome'] ?? '-') . '</td>';

                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="7">Nenhum usuário encontrado.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="container">
            <div class="table-header">
                <h2>Turmas</h2>
                <a href="turmasDash.php" class="ver-todos-btn">
                    Ver todos ➜
                </a>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome do Curso</th>
                            <th>Alunos Relacionados</th>
                            <th>Semestre</th>
                        </tr>
                    </thead>
                    <tbody id="turmas-tbody">
                        <?php
                        if ($resultTurmas && $resultTurmas->num_rows > 0) {
                            while ($turma = $resultTurmas->fetch_assoc()) {
                                // Definir sigla conforme ID do curso
                                $siglaCurso = '';
                                switch ($turma['curso_id']) {
                                    case 1:
                                        $siglaCurso = 'DSM';
                                        break;
                                    case 2:
                                        $siglaCurso = 'GE';
                                        break;
                                    case 3:
                                        $siglaCurso = 'GPI';
                                        break;
                                }

                                // Exemplo: DSM-1, GE-2 etc.
                                $idFormatado = $siglaCurso . '-' . preg_replace('/[^0-9]/', '', $turma['semestre_nome']);

                                echo '<tr>';
                                echo '  <td>' . $idFormatado . '</td>';
                                echo '  <td>' . htmlspecialchars($turma['curso_nome']) . '</td>';
                                echo '  <td>' . $turma['qtd_alunos'] . '</td>';
                                echo '  <td>' . htmlspecialchars($turma['semestre_nome']) . '</td>';

                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="5">Nenhuma turma encontrada.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>



        <!-- Modal do Criar Eleicao -->
        <form method="POST">
            <input type="hidden" name="criar_eleicao" value="1"></div>
            <div id="modalOverlay" class="modal-overlay">
                <div class="modalCriar">
                    <div class="modal-left">
                        <div class="election-title">
                            <span onclick="fecharModalCriar()" class="close-btn">✖</span>

                            <h2>CRIAR ELEIÇÃO</h2>
                        </div>
                        <div class="form-group">
                            <label>Título</label>
                            <input type="text" placeholder="Eleição de Representante de Turma" class="form-control"
                                name="titulo" required>

                        </div>
                        <div class="form-group">
                            <label>Descrição</label>
                            <textarea rows="5" class="form-control"
                                placeholder="Votação para eleição do primeiro representante de sala da turma de 1º DSM Noturno"
                                name="descricao"></textarea>

                        </div>
                        <div class="form-group">
                            <label>Curso e Semestre:</label>
                            <div class="form-row">
                                <div class="form-group">
                                    <select class="form-control" name="curso_id" required>
                                        <?php foreach ($cursos as $c): ?>
                                            <option value="<?= $c['id'] ?>"><?= $c['nome'] ?></option>
                                        <?php endforeach; ?>
                                    </select>

                                </div>
                                <div class="form-group">
                                    <select class="form-control" name="semestre_id" required>
                                        <?php foreach ($semestres as $s): ?>
                                            <option value="<?= $s['id'] ?>"><?= $s['nome'] ?></option>
                                        <?php endforeach; ?>
                                    </select>

                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Início:</label>
                            <div class="form-row">
                                <div class="form-group">
                                    <input type="date" class="form-control" name="data_inicio_data" required>

                                </div>
                                <div class="form-group">
                                    <input type="time" class="form-control" name="data_inicio_hora" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Fim:</label>
                            <div class="form-row">
                                <div class="form-group">
                                    <input type="date" class="form-control" name="data_fim_data" required>
                                </div>
                                <div class="form-group">
                                    <input type="time" class="form-control" name="data_fim_hora" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="submit-btn">CRIAR ELEIÇÃO</button>
                    </div>
                </div>
            </div>
        </form>


        <!-- Modal do Editar Eleicao -->
        <div id="modalEditarOverlay" class="modal-overlay" style="display:none;">
            <div class="modal">

                <!-- LADO ESQUERDO -->
                <div class="modal-left">

                    <div class="election-title">
                        <h2>EDITAR ELEIÇÃO</h2>
                    </div>

                    <form id="formEditar" method="POST">

                        <input type="hidden" name="editar_eleicao" value="1">
                        <input type="hidden" id="editId" name="id">

                        <div class="form-group">
                            <label>Nome:</label>
                            <input type="text" id="editTitulo" name="titulo" class="form-control" readonly
                                style="background-color: #bbb;">
                        </div>

                        <div class="form-group">
                            <label>Descrição:</label>
                            <textarea id="editDescricao" name="descricao" class="form-control" rows="5"
                                style="background-color: #bbb;" readonly></textarea>
                        </div>

                        <div class="two-col">
                            <div class="form-group">
                                <label>Curso:</label>
                                <input type="text" id="editCurso" class="form-control" style="background-color: #bbb;"
                                    readonly>
                            </div>

                            <div class="form-group">
                                <label>Semestre:</label>
                                <input type="text" id="editSemestre" class="form-control"
                                    style="background-color: #bbb;" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Datas:</label>
                            <div class="form-row">

                                <div class="form-group">
                                    <input type="datetime-local" style="background-color: #bbb;" id="editInicio"
                                        name="data_inicio" class="form-control" readonly>
                                </div>

                                <div class="form-group">
                                    <input type="datetime-local" id="editFim" name="data_fim" class="form-control">
                                </div>

                            </div>
                        </div>

                        <div class="atention-card">
                            <p>Você só pode alterar a data de fim.</p>
                        </div>

                        <button class="submit-btn" type="submit">SALVAR ALTERAÇÕES</button>

                    </form>

                </div>

                <div class="modal-right">

                    <button onclick="fecharModalEditar()" class="close-btn2">✖</button>
                    <h2>Candidatos</h2>

                    <div id="candidatos-editar-container" class="candidate-list2"></div>
                </div>

            </div>
        </div>


        <script defer>
            function abrirEditar(nome) {
                alert(`Editando Usuário: ${nome}`);
            }

            function excluirUsuario(ra) {
                if (confirm(`Tem certeza que deseja excluir o usuário com RA ${ra}?`)) {
                    alert(`Usuário ${ra} excluído!`);
                }
            }

            function editarTurma(id) {
                alert(`Editando Turma: ${id}`);
            }

            function excluirTurma(id) {
                if (confirm(`Tem certeza que deseja excluir a turma ${id}?`)) {
                    alert(`Turma ${id} excluída!`);
                }
            }

            function abrirModalEditar(id) {

                document.body.style.overflow = "hidden"; 
                fetch("dashboard.php?get_eleicao=" + id)
                    .then(res => res.json())
                    .then(data => {

                        if (data.erro) {
                            alert(data.erro);
                            return;
                        }

                        let e = data.eleicao;

                        // Preencher campos
                        document.getElementById("editId").value = e.id;
                        document.getElementById("editTitulo").value = e.titulo;
                        document.getElementById("editDescricao").value = e.descricao;

                        document.getElementById("editInicio").value = e.data_inicio.replace(" ", "T");
                        document.getElementById("editFim").value = e.data_fim.replace(" ", "T");

                        // NOVOS CAMPOS
                        document.getElementById("editCurso").value = e.curso_nome;

                        // transformar semestre para número apenas
                        let sem = e.semestre_nome.replace(/[^0-9]/g, "");
                        document.getElementById("editSemestre").value = sem;

                        let container = document.getElementById("candidatos-editar-container");
                        container.innerHTML = "";

                        if (data.candidatos.length === 0) {
                            container.innerHTML = "<p>Nenhum candidato encontrado.</p>";
                        } else {
                            data.candidatos.forEach(c => {
                                container.innerHTML += `
                        <div class="candidate-item2">
                            <strong>${c.nome}</strong>
                            <span>RA: ${c.ra}</span>
                        </div>
                    `;
                            });
                        }

                        document.getElementById("modalEditarOverlay").style.display = "flex";
                    });
            }


            // Abrir modal de criação
            document.getElementById("criarMais").addEventListener("click", () => {
                document.getElementById("modalOverlay").style.display = "flex";
                document.body.style.overflow = "hidden"; 
            });

            // Fechar modal clicando fora
            document.getElementById("modalOverlay").addEventListener("click", (e) => {
                if (e.target.id === "modalOverlay") {
                    e.target.style.display = "none";
                    document.body.style.overflow = "auto"; 
                }
            });

            function fecharModalCriar() {
                document.getElementById('modalOverlay').style.display = 'none';
                document.body.style.overflow = "auto"; 
            }
            function fecharModalEditar() {
                document.getElementById('modalEditarOverlay').style.display = 'none';
                document.body.style.overflow = "auto"; 
            }


            // Abrir modal de edição
            document.querySelectorAll(".btn-edit").forEach(btn => {
                btn.addEventListener("click", () => {
                    document.getElementById("modalEditarOverlay").style.display = "flex";
                    document.body.style.overflow = "hidden"; 
                });
            });

            // Fechar modal de edição clicando fora
            document.getElementById("modalEditarOverlay").addEventListener("click", (e) => {
                if (e.target.id === "modalEditarOverlay") {
                    e.target.style.display = "none";
                    document.body.style.overflow = "auto"; 
                }
            });

        </script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
           <script src="../php/gerarPDF.js"></script>

    </main>

    <footer class="footer">
        <div class="footer-top">
            <div class="footer-logo">
                <img src="../Images/logoFaVote.png" width="70">
            </div>
            <div class="footer-links">
                <div>
                    <h4>PÁGINAS</h4>
                    <ul>
                        <li><a href="home.php">Home</a></li>
                        <li><a href="eleAtive.php">Eleições Ativas</a></li>
                        <li><a href="news.php">Notícias</a></li>
                        <li><a href="elePassa.php">Eleições Passadas</a></li>
                        <li><a href="termos.php">Termos de Contrato</a></li>
                    </ul>
                </div>
                <div>
                    <h4>REDES</h4>
                    <ul>
                        <li><a href="https://www.instagram.com/fatecdeitapira" target="_blank" rel="noopener noreferrer">Instagram</a></li>
                        <li><a href="https://www.facebook.com/share/16Y3jKo71m/" target="_blank" rel="noopener noreferrer">Facebook</a></li>
                        <li><a href="https://www.youtube.com/@fatecdeitapiraogaridecastr2131" target="_blank" rel="noopener noreferrer">YouTube</a></li>
                        <li><a href="https://www.linkedin.com/school/faculdade-estadual-de-tecnologia-de-itapira-ogari-de-castro-pacheco/about/" target="_blank" rel="noopener noreferrer">LinkedIn</a></li>
                        <li><a href="https://fatecitapira.cps.sp.gov.br/" target="_blank" rel="noopener noreferrer">Site Fatec</a></li>
                    </ul>
                </div>
                <div>
                    <h4>INTEGRANTES</h4>
                    <ul>
                        <li>João Paulo Gomes</li>
                        <li>João Pedro Baradeli Pavan</li>
                        <li>Pedro Henrique Cavenaghi dos Santos</li>
                        <li>Samuel Santos Oliveira</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            FaVote - Todos os direitos reservados | 2025
        </div>
    </footer>

</body>

</html>