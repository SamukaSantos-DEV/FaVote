<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = $_POST['nome_cadastro'];
    $email = $_POST['email_cadastro'];
    $ra = $_POST['ra_cadastro'];
    $senha = $_POST['senha_cadastro'];

    if (empty($nome) || empty($email) || empty($ra) || empty($senha)) {
        header("Location: ../login.php?error=empty_fields");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@fatec\.sp\.gov\.br$/', $email)) {
        header("Location: ../login.php?error=invalid_email");
        exit();
    }

    if (!preg_match('/^\d{13}$/', $ra)) {
        header("Location: ../login.php?error=invalid_ra");
        exit();
    }

    // ===================================================================
    // INTERPRETAÇÃO DO RA
    // 2781392513007
    // 278 - prefixo (ignorar)
    // 139 - código do curso
    // 25  - ano de ingresso (2025)
    // 1   - semestre de ingresso
    // 3007 - identificação individual
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

    // semestre do curso atual
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

        $stmt->close();
        $conexao->close();

        header("Location: ../login.php?success=registered");
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
