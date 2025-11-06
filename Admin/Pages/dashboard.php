<?php

include '../../php/config.php';

$sqlEleicoesAtivas = "SELECT * FROM eleicoes WHERE ativa != 0";
$resultAtivas = $conexao->query($sqlEleicoesAtivas);

$sqlEleicoesPassadas = "SELECT * FROM eleicoes WHERE ativa = 0";
$resultPassadas = $conexao->query($sqlEleicoesPassadas);

$sqlNoticias = "SELECT * FROM noticias ORDER BY dataPublicacao DESC";
$resultNoticias = $conexao->query($sqlNoticias);

$sqlUsuarios = "
    SELECT 
        a.id AS aluno_id,
        a.ra,
        a.nome,
        a.email_institucional,
        a.cpf,
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
                <strong>Usuário Administrador</strong>
                <p>FATEC “Dr. Ogari de Castro Pacheco”</p>
                <strong>
                    <p>DSM (N)</p>
                </strong>
                <p>1º Semestre</p>

                <div class="editar">
                    <a href="editardados.php">Editar dados<i class="fa-solid fa-pen-to-square"
                            style="margin-left: 7px;"></i></a>
                </div>
                <div class="sair">
                    <a href="../../login.php">Sair<i style="margin-left: 5px;"
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
                            <span>ID</span>
                            <span><?= htmlspecialchars($row['id']) ?></span>
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
                                <button type="button" id="btnEditar<?= $row['id'] ?>" class="btn-edit">
                                    <i class="fas fa-edit" style="margin-right: 5px;"></i> EDITAR
                                </button>

                                <a href="dashboard.php?id=<?= $row['id'] ?>"
                                    class="btn-cancel"
                                    id="btnCancelar<?= $row['id'] ?>"
                                    onclick="return confirm('AVISO: Você está prestes a cancelar/excluir “<?= htmlspecialchars($row['titulo']) ?>”, criada em <?= date('d/m/Y H:i', strtotime($row['dataPostagem'])) ?>')">
                                    <i class="fas fa-times" style="margin-right: 5px;"></i> Excluir
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="ver-mais">
                    <label class="btn-ver-mais">Ver mais <i class="fas fa-chevron-down"
                            style="margin-left: 5px;"></i></label>
                </div>
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
                            <span>ID</span>
                            <span><?= htmlspecialchars($row['id']) ?></span>
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
                                <a href="../Docs/Ata.docx"
                                    target="_blank"
                                    class="btn-pdf"
                                    id="btnPdf<?= $row['id'] ?>">
                                    <i class="fa-solid fa-download" style="margin-right: 5px;"></i> Ata
                                </a>

                                <a href="dashboard.php?id=<?= $row['id'] ?>"
                                    class="btn-delete"
                                    id="btnExcluir<?= $row['id'] ?>"
                                    onclick="return confirm('AVISO: Você está prestes a cancelar/excluir “<?= htmlspecialchars($row['titulo']) ?>”, criada em <?= date('d/m/Y H:i', strtotime($row['dataPostagem'])) ?>')">
                                    <i class="fas fa-trash" style="margin-right: 5px;"></i> Excluir
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="ver-mais">
                    <label class="btn-ver-mais">Ver mais <i class="fas fa-chevron-down"
                            style="margin-left: 5px;"></i></label>
                </div>
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
                    <div class="news-filter">
                        <p class="filter-active">Mais recentes</p>
                    </div>
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
                                <a href="dashboard.php?id=<?= $noticia['id'] ?>"
                                    class="delete-btn"
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
                <div class="ver-mais">
                    <label class="btn-ver-mais">
                        Ver mais <i class="fas fa-chevron-down" style="margin-left: 5px;"></i>
                    </label>
                </div>
            <?php endif; ?>
        </div>


        <div class="container" style="margin-top: 5%;">
            <div class="table-header">
                <h2>Usuários</h2>
                <a href="turmausuario.php" class="ver-todos-btn">
                    Ver todos ➜
                </a>

            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>RA</th>
                            <th>Nome</th>
                            <th>E-Mail</th>
                            <th>CPF</th>
                            <th>Curso</th>
                            <th>Período</th>
                            <th>Ações</th>
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
                                echo '  <td>' . htmlspecialchars($usuario['cpf']) . '</td>';
                                echo '  <td>' . htmlspecialchars($usuario['curso_nome'] ?? '-') . '</td>';
                                echo '  <td>' . htmlspecialchars($usuario['semestre_nome'] ?? '-') . '</td>';
                                echo '  <td>';
                                echo '      <div class="actions">';
                                echo '          <button class="action-btn tableEdit-btn" title="Editar" onclick="abrirEditar(\'' . addslashes($usuario['nome']) . '\')">✎</button>';
                                echo '          <button class="action-btn tabledelete-btn" title="Excluir" onclick="excluirUsuario(\'' . addslashes($usuario['ra']) . '\')">🗑</button>';
                                echo '      </div>';
                                echo '  </td>';
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
                <a href="turmausuario.php" class="ver-todos-btn">
                    Ver todos ➜
                </a>

            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome do Curso</th>
                            <th>Qtd de Alunos Relacionados</th>
                            <th>Semestre</th>
                            <th>Ações</th>
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
                                echo '  <td>';
                                echo '      <div class="actions">';
                                echo '          <button class="action-btn tableEdit-btn" title="Editar" onclick="editarTurma(\'' . $turma['turma_id'] . '\')">✎</button>';
                                echo '          <button class="action-btn tabledelete-btn" title="Excluir" onclick="excluirTurma(\'' . $turma['turma_id'] . '\')">🗑</button>';
                                echo '      </div>';
                                echo '  </td>';
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


        <!-- Modal do + Criar Nova Eleicao -->

        <div id="modalOverlay" class="modal-overlay">
            <div class="modal">

                <div class="modal-left">
                    <div class="election-title">
                        <h2>CRIAR ELEIÇÃO</h2>
                    </div>
                    <div class="form-group">
                        <label for="nome">Nome:</label>
                        <input type="text" id="nome" value="ELEIÇÃO de Representante de Turma 1° DSM"
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="descricao">Descrição:</label>
                        <textarea id="descricao" class="form-control"
                            rows="5">Votação para eleição do primeiro representante de sala da turma de 1º DSM Noturno</textarea>

                    </div>
                    <div class="form-group">
                        <label>Curso e Semestre:</label>
                        <div class="form-row">
                            <div class="form-group">
                                <select class="form-control">
                                    <option value="">DSM (N)</option>
                                    <option value="">GE (N)</option>
                                    <option value="">GPI (N)</option>

                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control">
                                    <option value="">1° Semestre</option>
                                    <option value="">2° Semestre</option>
                                    <option value="">3° Semestre</option>
                                    <option value="">4° Semestre</option>
                                    <option value="">5° Semestre</option>
                                    <option value="">6° Semestre</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Início:</label>
                        <div class="form-row">
                            <div class="form-group">
                                <input type="date" class="form-control">
                            </div>
                            <div class="form-group">
                                <input type="time" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Fim:</label>
                        <div class="form-row">
                            <div class="form-group">
                                <input type="date" class="form-control">
                            </div>
                            <div class="form-group">
                                <input type="time" class="form-control">
                            </div>
                        </div>
                    </div>
                    <button class="submit-btn" onclick="criarEleicao()">CRIAR ELEIÇÃO</button>
                </div>
                <div class="modal-right">
                    <h2>CANDIDATOS</h2>
                    <div class="search-candidate">
                        <input type="text" placeholder="Pesquisar candidato...">
                    </div>
                    <h3>Listagem</h3>
                    <div class="candidate-header">
                        <label for="selectAll">Selecionar Todos</label>
                        <input type="checkbox" id="selectAll" class="candidate-checkbox select-all">
                    </div>
                    <div id="candidatos-container" class="candidate-list"></div>

                </div>
            </div>
        </div>
        </div>


        <!-- Modal do Editar Eleicao -->
        <div id="modalEditarOverlay" class="modal-overlay" style="display: none;">
            <div class="modal">
                <div class="modal-left">
                    <div class="election-title">
                        <h2>EDITAR ELEIÇÃO</h2>
                    </div>

                    <div class="form-group">
                        <label for="nome">Nome:</label>
                        <input type="text" id="nome" value="ELEIÇÃO de Representante de Turma 1° DSM"
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="descricao">Descrição:</label>
                        <textarea id="descricao" class="form-control"
                            rows="5">Votação para eleição do primeiro representante de sala da turma de 1º DSM Noturno</textarea>

                    </div>
                    <div class="form-group">
                        <label>Curso e Semestre:</label>
                        <div class="form-row">
                            <div class="form-group">
                                <select class="form-control">
                                    <option value="">DSM (N)</option>
                                    <option value="">GE (N)</option>
                                    <option value="">GPI (N)</option>

                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control">
                                    <option value="">1° Semestre</option>
                                    <option value="">2° Semestre</option>
                                    <option value="">3° Semestre</option>
                                    <option value="">4° Semestre</option>
                                    <option value="">5° Semestre</option>
                                    <option value="">6° Semestre</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Datas de Início e Fim:</label>
                        <div class="form-row">
                            <div class="form-group">
                                <input type="datetime-local" id="editInicio" class="form-control">
                            </div>
                            <div class="form-group">
                                <input type="datetime-local" id="editFim" class="form-control">
                            </div>
                        </div>
                    </div>


                    <div class="atention-card">
                        <p>ATENÇÃO: Ao editar uma eleição, você só pode alterar a data e hora do fim do processo,
                            para caso de prorrogação. Para mais alterações, crie uma nova eleição e cancele a atual,
                            para que os votos sejam descontabilizados! Edição do Início da Eleição disponível apenas
                            antes do período de iniciar.
                        </p>
                    </div>

                    <button class="submit-btn" onclick="salvarAlteracoes()">SALVAR ALTERAÇÕES</button>
                </div>

                <div class="modal-right">
                    <h2>Candidatos</h2>
                    <div class="search-candidate">
                        <input type="text" placeholder="Pesquisar candidato...">
                    </div>


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

            document.addEventListener('DOMContentLoaded', () => {
                criarLinhasTabela();
                criarLinhasTurmas();

                const candidatos = [
                    "Victor Luiz Rodrigues", "Ana Beatriz Silva", "João Pedro Oliveira", "Carlos Eduardo",
                    "Maria Fernanda", "Juliana Moura", "Felipe Andrade", "Larissa Costa", "Thiago Martins",
                    "Camila Ribeiro", "Eduardo Lima", "Patrícia Alves", "Gabriel Souza", "Renata Dias",
                    "Lucas Pereira", "Amanda Rocha", "Bruno Fernandes", "Natália Gomes", "Vinícius Castro",
                    "Letícia Mendes"
                ];


                function preencherCandidatos(containerId, marcarTodos = false) {
                    const container = document.getElementById(containerId);
                    container.innerHTML = "";
                    candidatos.forEach(nome => {
                        const partes = nome.trim().split(" ");
                        const iniciais = (partes[0][0] + partes[partes.length - 1][0]).toUpperCase();
                        const item = document.createElement("div");
                        item.className = "candidate-item";
                        const avatar = document.createElement("div");
                        avatar.className = "candidate-avatar";
                        avatar.textContent = iniciais;
                        const name = document.createElement("div");
                        name.className = "candidate-name";
                        name.textContent = nome;
                        const checkbox = document.createElement("input");
                        checkbox.type = "checkbox";
                        checkbox.className = "candidate-checkbox";
                        checkbox.checked = marcarTodos;
                        item.addEventListener("click", function() {
                            checkbox.checked = !checkbox.checked;
                        });
                        item.appendChild(avatar);
                        item.appendChild(name);
                        item.appendChild(checkbox);
                        container.appendChild(item);
                    });
                }

                function configurarSelecionarTodos(checkboxId, containerId) {
                    const selectAll = document.getElementById(checkboxId);
                    const container = document.getElementById(containerId);
                    selectAll.addEventListener("change", function() {
                        const checkboxes = container.querySelectorAll(".candidate-checkbox");
                        checkboxes.forEach(cb => cb.checked = this.checked);
                    });
                }


                const modalOverlay = document.getElementById('modalOverlay');
                const criarMaisBtn = document.getElementById('criarMais');

                criarMaisBtn.addEventListener('click', function() {
                    modalOverlay.style.display = 'flex';
                    preencherCandidatos("candidatos-container");
                });

                modalOverlay.addEventListener('click', function(event) {
                    if (event.target === modalOverlay) {
                        modalOverlay.style.display = 'none';
                    }
                });

                configurarSelecionarTodos("selectAll", "candidatos-container");

                const btnEditar1 = document.getElementById('btnEditar1');
                const btnEditar2 = document.getElementById('btnEditar2');
                const modalEditarOverlay = document.getElementById('modalEditarOverlay');

                btnEditar1.addEventListener('click', () => {
                    modalEditarOverlay.style.display = 'flex';
                    preencherCandidatos("candidatos-editar-container", true);
                });

                btnEditar2.addEventListener('click', () => {
                    modalEditarOverlay.style.display = 'flex';
                    preencherCandidatos("candidatos-editar-container", true);
                });



                modalEditarOverlay.addEventListener('click', (event) => {
                    if (event.target === modalEditarOverlay) {
                        modalEditarOverlay.style.display = 'none';
                    }
                });

                configurarSelecionarTodos("selectAllEditar", "candidatos-editar-container");
            });

            function salvarAlteracoes() {
                alert('Dados salvos com sucesso');
                const modalEditarOverlay = document.getElementById('modalEditarOverlay');
                modalEditarOverlay.style.display = 'none';
            }

            function criarEleicao() {
                alert('Eleição criada com sucesso');
                const modalOverlay = document.getElementById('modalOverlay');
                modalOverlay.style.display = 'none';
            }
        </script>
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
                        <li><a href="elepassa.php">Eleições Passadas</a></li>
                        <li><a href="termos.php">Termos de Contrato</a></li>
                    </ul>
                </div>
                <div>
                    <h4>REDES</h4>
                    <ul>
                        <li><a href="https://www.instagram.com/fatecdeitapira?igsh=MWUzNXMzcWNhZzB4Ng=="
                                target="_blank">Instagram</a></li>
                        <li><a href="https://www.facebook.com/share/16Y3jKo71m/" target="_blank">Facebook</a></li>
                        <li><a href="https://www.youtube.com/@fatecdeitapiraogaridecastr2131"
                                target="_blank">Youtube</a></li>
                        <li><a href="https://www.linkedin.com/school/faculdade-estadual-de-tecnologia-de-itapira-ogari-de-castro-pacheco/about/"
                                target="_blank">Linkedin</a></li>
                        <li><a href="https://fatecitapira.cps.sp.gov.br/" target="_blank">Site Fatec</a></li>
                    </ul>
                </div>
                <div>
                    <h4>INTEGRANTES</h4>
                    <ul>
                        <li>Graziela Dilany da Silva</li>
                        <li>João Pedro Baradeli Pavan</li>
                        <li>Pedro Henrique Cavenaghi dos Santos</li>
                        <li>Samara Stefani da Silva</li>
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