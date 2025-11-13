<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = $_POST['nome_cadastro'];
    $email = $_POST['email_cadastro'];
    $ra = $_POST['ra_cadastro'];
    $senha = $_POST['senha_cadastro'];

    // Verificações básicas
    if (empty($nome) || empty($email) || empty($ra) || empty($senha)) {
        header("Location: ../login.php?error=empty_fields");
        exit();
    }

    // Validação do e-mail institucional
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@fatec\.sp\.gov\.br$/', $email)) {
        header("Location: ../login.php?error=invalid_email");
        exit();
    }

    // RA precisa ter 13 dígitos
    if (!preg_match('/^\d{13}$/', $ra)) {
        header("Location: ../login.php?error=invalid_ra");
        exit();
    }

    // ===================================================================
    // INTERPRETAÇÃO DO RA
    // ===================================================================
    $codigoCurso = substr($ra, 3, 3);
    $anoIngresso = (int) substr($ra, 6, 2);
    $semestreIngresso = (int) substr($ra, 8, 1);

    // MAPEAMENTO CURSO_ID
    $cursos = [
        '139' => 1, // Desenvolvimento de Software Multiplataforma
        '064' => 2, // Gestão Empresarial
        '077' => 3, // Gestão de Produção Industrial
    ];

    if (!isset($cursos[$codigoCurso])) {
        header("Location: ../login.php?error=unknown_course");
        exit();
    }

    $curso_id = $cursos[$codigoCurso];

    // CALCULAR SEMESTRE ATUAL
    $anoAtual = (int) date("y");
    $semestreAtual = (date("n") <= 6) ? 1 : 2;

    $anosPassados = $anoAtual - $anoIngresso;
    $semestresPassados = ($anosPassados * 2) + ($semestreAtual - $semestreIngresso);
    $semestreCurso = min(6, max(1, $semestresPassados + 1));

    // BUSCAR TURMA_ID CORRESPONDENTE
    $sql_turma = "SELECT id FROM turmas WHERE curso_id = ? AND semestre_id = ?";
    $stmt_turma = $conexao->prepare($sql_turma);
    $stmt_turma->bind_param("ii", $curso_id, $semestreCurso);
    $stmt_turma->execute();
    $resultado_turma = $stmt_turma->get_result();

    if ($resultado_turma->num_rows === 0) {
        header("Location: ../login.php?error=turma_not_found");
        exit();
    }

    $turma = $resultado_turma->fetch_assoc();
    $turma_id = $turma['id'];
    $stmt_turma->close();

    // INSERÇÃO DO ALUNO
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO alunos (ra, nome, email_institucional, senha, turma_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssssi", $ra, $nome, $email, $senha_hash, $turma_id);
        $stmt->execute();
        $novo_id = $stmt->insert_id; // pega o ID do aluno recém-cadastrado
        $stmt->close();

        // 🔹 Buscar dados completos do aluno (curso, semestre, turma)
        $sql_info = "
            SELECT 
                a.*, 
                c.nome AS curso_nome, 
                s.nome AS semestre_nome, 
                t.id AS turma_id
            FROM alunos a
            LEFT JOIN turmas t ON a.turma_id = t.id
            LEFT JOIN cursos c ON t.curso_id = c.id
            LEFT JOIN semestres s ON t.semestre_id = s.id
            WHERE a.id = ?
        ";

        $stmt_info = $conexao->prepare($sql_info);
        $stmt_info->bind_param("i", $novo_id);
        $stmt_info->execute();
        $aluno = $stmt_info->get_result()->fetch_assoc();
        $stmt_info->close();

        // 🔹 Login automático
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $aluno['id'];
        $_SESSION['user_type'] = 'aluno';
        $_SESSION['user_email'] = $aluno['email_institucional'];
        $_SESSION['user_name'] = $aluno['nome'];
        $_SESSION['user_ra'] = $aluno['ra'];
        $_SESSION['curso_nome'] = $aluno['curso_nome'] ?? 'Curso não definido';
        $_SESSION['semestre_nome'] = $aluno['semestre_nome'] ?? 'Semestre não definido';
        $_SESSION['turma_id'] = $aluno['turma_id'];

        $conexao->close();

        // 🔹 Redirecionar direto para a home
        header("Location: ../Pages/home.php");
        exit();

    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            header("Location: ../login.php?error=user_exists");
            exit();
        } else {
            header("Location: ../login.php?error=db_error");
            exit();
        }
    }
} else {
    header("Location: ../login.php");
    exit();
}
?>
