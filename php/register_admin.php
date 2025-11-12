<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = $_POST['nome_cadastro'];
    $email = $_POST['email_cadastro'];
    $ra = $_POST['ra_cadastro'];
    $senha = $_POST['senha_cadastro'];
    $cpf = $_POST['cpf_cadastro']; // 1. CAMPO CPF ADICIONADO

    // 2. CPF ADICIONADO NA VALIDAÇÃO
    if (empty($nome) || empty($email) || empty($ra) || empty($senha) || empty($cpf)) {
        header("Location: ../Admin/login.php?error=empty_fields");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@fatec\.sp\.gov\.br$/', $email)) {
        header("Location: ../Admin/login.php?error=invalid_email");
        exit();
    }
    
    if (!preg_match('/^\d{13}$/', $ra)) {
         header("Location: ../Admin/login.php?error=invalid_ra");
         exit();
    }
    
    // 3. VALIDAÇÃO DO CPF (11 dígitos numéricos)
    if (!preg_match('/^\d{11}$/', $cpf)) {
         header("Location: ../Admin/login.php?error=invalid_cpf");
         exit();
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    try {
        // 4. SQL ATUALIZADO (com cpf)
        $sql = "INSERT INTO admin (nome_usuario, email, ra, cpf, senha) VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conexao->prepare($sql);
        
        // 5. BIND ATUALIZADO ("sssss" para 5 strings, incluindo $cpf)
        $stmt->bind_param("sssss", $nome, $email, $ra, $cpf, $senha_hash);
        
        $stmt->execute();
        $stmt->close();
        $conexao->close();

        header("Location: ../Admin/login.php?success=registered");
        exit();

    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
             header("Location: ../Admin/login.php?error=user_exists");
             exit();
        } else {
            header("Location: ../Admin/login.php?error=db_error");
            exit();
        }
    }
} else {
    header("Location: ../Admin/login.php");
    exit();
}
?>