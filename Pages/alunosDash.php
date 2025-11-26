<?php
require '../php/session_auth.php';
include '../php/config.php';

// === CONSULTA SEMESTRES ===
$sqlSemestres = "SELECT id, nome FROM semestres";
$resultSemestres = $conexao->query($sqlSemestres);

// Transforma em array para passar ao JS
$listaSemestres = [];
if ($resultSemestres->num_rows > 0) {
    while ($s = $resultSemestres->fetch_assoc()) {
        $listaSemestres[] = $s;
    }
}

// ==========================================================
// === LÓGICA DE BUSCA DINÂMICA (AJAX) ===
// Esta parte retorna JSON e encerra o script.
// ==========================================================
if (isset($_GET['termo_busca'])) {
    // Escapa o termo para ser usado no LIKE e adiciona os curingas %
    // Usamos str_contains para verificar se o termo não é totalmente vazio
    $termoBusca = $_GET['termo_busca'] ?? '';
    $termo = "%" . $termoBusca . "%";

    $sqlBusca = "
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
        WHERE a.ra LIKE ? 
           OR a.nome LIKE ? 
           OR a.email_institucional LIKE ?
           OR c.nome LIKE ?
           OR s.nome LIKE ?
    ";
    
    $stmt = $conexao->prepare($sqlBusca);
    
    // Bind dos parâmetros
    $stmt->bind_param("sssss", $termo, $termo, $termo, $termo, $termo);
    $stmt->execute();
    $resultBusca = $stmt->get_result();

    $usuariosFiltrados = [];
    while ($usuario = $resultBusca->fetch_assoc()) {
        $usuariosFiltrados[] = $usuario;
    }
    
    header('Content-Type: application/json');
    echo json_encode($usuariosFiltrados);
    exit; // Importante: termina a execução para não imprimir o HTML
}


// === AÇÕES PHP DE EDIÇÃO OU EXCLUSÃO ===
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao'])) {

    // === EDITAR USUÁRIO ===
    if ($_POST['acao'] === 'editar') {

        $id = $_POST['id'] ?? null;
        $nome = $_POST['nome'] ?? null;
        $email = $_POST['email'] ?? null;
        $semestreId = $_POST['semestre'] ?? null; // ← ADICIONADO

        // Verificar campos obrigatórios
        if ($id && $nome && $email && $semestreId) {

            // 1️⃣ Buscar a turma correspondente ao semestre
            $sqlTurma = $conexao->prepare("
                SELECT id 
                FROM turmas 
                WHERE semestre_id = ?
                LIMIT 1
            ");
            $sqlTurma->bind_param("i", $semestreId);
            $sqlTurma->execute();
            $result = $sqlTurma->get_result();

            if ($result->num_rows === 0) {
                echo "Nenhuma turma encontrada para esse semestre.";
                exit;
            }

            $turmaId = $result->fetch_assoc()['id'];

            // 2️⃣ Atualizar aluno com nome, email e turma
            $stmt = $conexao->prepare("
                UPDATE alunos 
                SET nome = ?, email_institucional = ?, turma_id = ?
                WHERE id = ?
            ");

            $stmt->bind_param("ssii", $nome, $email, $turmaId, $id);

            echo $stmt->execute()
                ? "Usuário atualizado com sucesso!"
                : "Erro ao atualizar usuário.";

        } else {
            echo "Preencha todos os campos.";
        }

        exit;
    }

    // === EXCLUIR USUÁRIO ===
    if ($_POST['acao'] === 'excluir') {
        $id = $_POST['id'] ?? null;

        if ($id) {

            $raAluno = null;

            // Buscar o RA do aluno
            $res = $conexao->prepare("SELECT ra FROM alunos WHERE id = ?");
            $res->bind_param("i", $id);
            $res->execute();
            $res->bind_result($raAluno);
            $res->fetch();
            $res->close();

            if ($raAluno) {

                // Excluir votos onde o aluno votou
                $stmt = $conexao->prepare("DELETE FROM votos WHERE aluno_ra = ?");
                $stmt->bind_param("s", $raAluno);
                $stmt->execute();
                $stmt->close();

                // Excluir votos que foram feitos em candidatos ligados ao aluno
                $stmt = $conexao->prepare("DELETE FROM votos 
                                             WHERE candidato_id IN (
                                                 SELECT id FROM candidatos WHERE aluno_ra = ?
                                             )");
                $stmt->bind_param("s", $raAluno);
                $stmt->execute();
                $stmt->close();

                // Excluir candidatura
                $stmt = $conexao->prepare("DELETE FROM candidatos WHERE aluno_ra = ?");
                $stmt->bind_param("s", $raAluno);
                $stmt->execute();
                $stmt->close();

                // Excluir aluno
                $stmt = $conexao->prepare("DELETE FROM alunos WHERE id = ?");
                $stmt->bind_param("i", $id);
                $ok = $stmt->execute();
                $stmt->close();

                echo $ok
                    ? "Usuário (e candidatura, se havia) excluído com sucesso!"
                    : "Erro ao excluir usuário.";

            } else {
                echo "Aluno não encontrado.";
            }

        } else {
            echo "ID inválido.";
        }

        exit;
    }
}



// === CONSULTA USUÁRIOS (Para o carregamento inicial da página) ===
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
";
$resultUsuarios = $conexao->query($sqlUsuarios);

// === CONSULTA TURMAS (Mantida) ===
$sqlTurmas = "
    SELECT 
        t.id AS turma_id,
        c.id AS curso_id,
        c.nome AS curso_nome,
        s.nome AS semestre_nome,
        (SELECT COUNT(*) FROM alunos a WHERE a.turma_id = t.id) AS qtd_alunos
    FROM turmas t
    LEFT JOIN cursos c ON t.curso_id = c.id
    LEFT JOIN semestres s ON t.semestre_id = s.id
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
    <link rel="icon" href="../Images/logoFaVote.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<script>
    const semestresDB = <?php echo json_encode($listaSemestres); ?>;
</script>


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

                $emailLogado = $_SESSION['user_email'] ?? null;
                ?>

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

    <script>
        window.addEventListener('scroll', function () {
            const btn = document.querySelector('.btn-close');
            if (window.scrollY > 50) {
                btn.classList.add('scrolled');
            } else {
                btn.classList.remove('scrolled');
            }
        });
    </script>
    
    <div class="main-content">

    <button class="btn-close" onclick="history.back()">➜</button>

<div class="headerTabela">
            <h2>Alunos</h2>
            <input type="text" name="pesquisar" id="input-pesquisa" placeholder="Pesquisar...">
            <div class="containerLupa">
            <img src="../Images/lupa.png">
            </div>
</div>
    <div class="containerAlunos" style="margin-top: 50px; width: 100%;">
        

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>RA</th>
                        <th>Nome</th>
                        <th>E-Mail</th>
                        <th>Curso</th>
                        <th>Período</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="usuarios-tbody">
                    <?php if ($resultUsuarios->num_rows > 0): ?>
                        <?php foreach ($resultUsuarios as $usuario): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($usuario['ra']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['email_institucional']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['curso_nome']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['semestre_nome']); ?></td>
                                <td>
                                    <div class="actions">
                                        <button class="action-btn tableEdit-btn" title="Editar"
                                            onclick="editarUsuario('<?php echo $usuario['aluno_id']; ?>')">✎</button>
                                        <button class="action-btn tabledelete-btn" title="Excluir"
                                            onclick="excluirUsuario('<?php echo $usuario['aluno_id']; ?>')">🗑</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">Nenhum usuário encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
    
    <script defer>
        const turmas = [
            // Você precisa popular esta variável 'turmas' no seu PHP/JS para que esta função funcione.
            // Mantendo a função, mas observando que a variável 'turmas' não está definida aqui.
        ];

        function criarLinhasTurmas() {
            const tbody = document.getElementById('turmas-tbody');
            if (tbody && typeof turmas !== 'undefined') {
                turmas.forEach(turma => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                <td>${turma.id}</td>
                <td>${turma.curso}</td>
                <td>${turma.qtdAlunos}</td>
                <td>${turma.semestre}</td>
                <td>
                    <div class="actions">
                        <button class="action-btn tableEdit-btn" title="Editar" onclick="editarTurma('${turma.id}')">✎</button>
                        <button class="action-btn tabledelete-btn" title="Excluir" onclick="excluirTurma('${turma.id}')">🗑</button>
                    </div>
                </td>
            `;
                    tbody.appendChild(tr);
                });
            }
        }
    </script>
    
    <script>
        // Função utilitária para criar a linha da tabela a partir de um objeto usuário
        function criarLinhaTabela(usuario) {
            return `
                <tr>
                    <td>${usuario.ra}</td>
                    <td>${usuario.nome}</td>
                    <td>${usuario.email_institucional}</td>
                    <td>${usuario.curso_nome}</td>
                    <td>${usuario.semestre_nome}</td>
                    <td>
                        <div class="actions">
                            <button class="action-btn tableEdit-btn" title="Editar"
                                onclick="editarUsuario('${usuario.aluno_id}')">✎</button>
                            <button class="action-btn tabledelete-btn" title="Excluir"
                                onclick="excluirUsuario('${usuario.aluno_id}')">🗑</button>
                        </div>
                    </td>
                </tr>
            `;
        }
        
        // ==========================================================
        // === LÓGICA DE BUSCA DINÂMICA (AJAX) ===
        // ==========================================================
        const inputPesquisa = document.getElementById('input-pesquisa');
        const usuariosTbody = document.getElementById('usuarios-tbody');

        function buscarUsuarios() {
            const termo = inputPesquisa.value.trim();
            // Envia o termo via GET para o próprio script PHP
            const url = `?termo_busca=${encodeURIComponent(termo)}`; 

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    // Limpa a tabela atual
                    usuariosTbody.innerHTML = ''; 

                    if (data.length > 0) {
                        // Adiciona as novas linhas filtradas
                        data.forEach(usuario => {
                            usuariosTbody.innerHTML += criarLinhaTabela(usuario);
                        });
                    } else {
                        // Mensagem de "não encontrado"
                        usuariosTbody.innerHTML = `<tr><td colspan="7">Nenhum usuário encontrado para o termo "${termo}".</td></tr>`;
                    }
                })
                .catch(error => {
                    console.error('Erro na busca dinâmica:', error);
                    usuariosTbody.innerHTML = `<tr><td colspan="7">Falha ao buscar dados. Tente novamente.</td></tr>`;
                });
        }
        
        // Adiciona o event listener para o evento 'input' (disparado a cada tecla digitada)
        document.addEventListener('DOMContentLoaded', () => {
            if (inputPesquisa) {
                inputPesquisa.addEventListener('input', buscarUsuarios);
            }
        });
        
        
        // ==========================================================
        // === LÓGICA DE EDIÇÃO E EXCLUSÃO ===
        // ==========================================================
        function editarUsuario(id) {
            const row = event.target.closest('tr');
            const cells = row.querySelectorAll('td');

            const ra = cells[0].innerText;
            const nome = cells[1].innerText;
            const email = cells[2].innerText;
            const curso = cells[3].innerText;
            const semestre = cells[4].innerText; 

            // CRIAR <select> do semestre
            let selectSemestre = `<select id="edit-semestre-${id}" style="padding:4px;">`;

            semestresDB.forEach(s => {
                const selected = (s.nome === semestre) ? "selected" : "";
                selectSemestre += `<option value="${s.id}" ${selected}>${s.nome}</option>`;
            });

            selectSemestre += `</select>`;


            // Substitui por inputs editáveis
            row.innerHTML = `
        <td><input type="text" value="${ra}" readonly style="background:#eee; border:none; width:90px;"></td>
        <td><input type="text" value="${nome}" id="edit-nome-${id}" style="width:150px;"></td>
        <td><input type="text" value="${email}" id="edit-email-${id}" style="width:200px;"></td>
        <td>${curso}</td>
        <td>${selectSemestre}</td>
        <td>
            <button onclick="salvarEdicao(${id})" class="tableSave-btn">💾</button>
            <button onclick="cancelarEdicao()" class="tableCancel-btn">↩</button>
        </td>`;

        }

        function cancelarEdicao() {
            location.reload();
        }

        function salvarEdicao(id) {
            const nome = document.getElementById(`edit-nome-${id}`).value.trim();
            const email = document.getElementById(`edit-email-${id}`).value.trim();
            const semestre = document.getElementById(`edit-semestre-${id}`).value; // 🔥 novo campo

            if (!nome || !email || !semestre) {
                alert("Preencha todos os campos!");
                return;
            }

            fetch("", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `acao=editar&id=${id}&nome=${encodeURIComponent(nome)}&email=${encodeURIComponent(email)}&semestre=${semestre}`
            })
                .then(res => res.text())
                .then(msg => {
                    alert(msg);
                    location.reload();
                })
                .catch(() => alert("Erro na comunicação com o servidor."));
        }


        function excluirUsuario(id) {
            if (!confirm("Deseja realmente excluir este usuário?")) return;

            fetch("", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `acao=excluir&id=${id}`
            })
                .then(res => res.text())
                .then(msg => {
                    alert(msg);
                    location.reload();
                })
                .catch(() => alert("Erro ao excluir usuário."));
        }
    </script>
    
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