<?php
session_unset();
session_destroy();
session_start();

require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email_login']);
    $senha = trim($_POST['senha_login']);

    // Verifica campos vazios
    if (empty($email) || empty($senha)) {
        header("Location: ../login.php?error=empty_fields");
        exit();
    }

    try {
        $user = null;
        $user_type = null;

        // 🔹 Consulta de aluno (verifique se o campo email_institucional existe na tabela)
        $sql_aluno = "
            SELECT 
                a.*, 
                c.nome AS curso_nome, 
                s.nome AS semestre_nome,
                t.id AS turma_id
            FROM alunos a
            LEFT JOIN turmas t ON a.turma_id = t.id
            LEFT JOIN cursos c ON t.curso_id = c.id
            LEFT JOIN semestres s ON t.semestre_id = s.id
            WHERE a.email_institucional = ?
        ";

        $stmt_aluno = $conexao->prepare($sql_aluno);
        $stmt_aluno->bind_param("s", $email);
        $stmt_aluno->execute();
        $result_aluno = $stmt_aluno->get_result();
        $aluno = $result_aluno->fetch_assoc();
        $stmt_aluno->close();

        // 🔹 Teste de senha do aluno
        if ($aluno) {
            $senha_correta = (
                $aluno['senha'] === $senha ||
                password_verify($senha, $aluno['senha'])
            );

            if ($senha_correta) {
                $user = $aluno;
                $user_type = 'aluno';
            }
        }

        // 🔹 Se não for aluno, verifica admin
        if (!$user) {
            $sql_admin = "SELECT * FROM admin WHERE email = ?";
            $stmt_admin = $conexao->prepare($sql_admin);
            $stmt_admin->bind_param("s", $email);
            $stmt_admin->execute();
            $result_admin = $stmt_admin->get_result();
            $admin = $result_admin->fetch_assoc();
            $stmt_admin->close();

            if ($admin) {
                $senha_correta = (
                    $admin['senha'] === $senha ||
                    password_verify($senha, $admin['senha'])
                );

                if ($senha_correta) {
                    $user = $admin;
                    $user_type = 'admin';
                }
            }
        }

        // 🔹 Login bem-sucedido
        if ($user) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user_type;

            // Define e-mail de forma segura
            $_SESSION['user_email'] = isset($user['email'])
                ? $user['email']
                : ($user['email_institucional'] ?? '');

            // Admin
            if ($user_type == 'admin') {
                $_SESSION['user_name'] = $user['nome_usuario'];
                $conexao->close();
                header("Location: ../Pages/home.php");
                exit();
            }

            // Aluno
            $_SESSION['user_name'] = $user['nome'];
            $_SESSION['user_ra'] = $user['ra'] ?? '';
            $_SESSION['curso_nome'] = $user['curso_nome'] ?? 'Curso não definido';
            $_SESSION['semestre_nome'] = $user['semestre_nome'] ?? 'Semestre não definido';
            $_SESSION['turma_id'] = $user['turma_id'];

            $conexao->close();
            header("Location: ../Pages/home.php");
            exit();
        }

        // 🔹 Caso falhe o login
        $conexao->close();
        header("Location: ../login.php?error=invalid_credentials");
        exit();

    } catch (mysqli_sql_exception $e) {
        // Erro no banco
        header("Location: ../login.php?error=db_error");
        exit();
    }

} else {
    header("Location: ../login.php");
    exit();
}
