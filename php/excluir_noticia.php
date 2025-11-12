<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_name'])) {
    header("Location: ../Pages/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Verifica se o ID existe
    $checkSql = "SELECT * FROM noticias WHERE id = ?";
    $stmt = $conexao->prepare($checkSql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        // Exclui a notícia
        $sql = "DELETE FROM noticias WHERE id = ?";
        $deleteStmt = $conexao->prepare($sql);
        $deleteStmt->bind_param("i", $id);

        if ($deleteStmt->execute()) {
            header("Location: ../Pages/dashboard.php?success=noticia_excluida");
            exit();
        } else {
            header("Location: ../Pages/dashboard.php?error=erro_excluir");
            exit();
        }
    } else {
        header("Location: ../Pages/dashboard.php?error=noticia_nao_encontrada");
        exit();
    }
} else {
    header("Location: ../Pages/dashboard.php?error=id_invalido");
    exit();
}