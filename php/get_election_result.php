<?php
// Define o cabeçalho para retornar JSON
header('Content-Type: application/json; charset=utf-8');

// Inclui a conexão (MySQLi)
require 'config.php';

try {
    // Verifica se o ID foi passado
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception("ID da eleição não fornecido.");
    }

    $eleicaoId = intval($_GET['id']);

    // 1. BUSCAR DADOS DA ELEIÇÃO E DO CURSO
    // Como a tabela 'eleicoes' não tem 'curso_id', fazemos um JOIN complexo
    // para pegar o curso do primeiro candidato associado a essa eleição.
    $sqlEleicao = "
        SELECT 
            e.titulo, 
            e.data_inicio, 
            e.data_fim,
            c.nome AS nome_curso
        FROM eleicoes e
        LEFT JOIN candidatos ca ON e.id = ca.eleicao_id
        LEFT JOIN alunos a ON ca.aluno_ra = a.ra
        LEFT JOIN turmas t ON a.turma_id = t.id
        LEFT JOIN cursos c ON t.curso_id = c.id
        WHERE e.id = ?
        LIMIT 1
    ";

    $stmt = $conexao->prepare($sqlEleicao);
    $stmt->bind_param("i", $eleicaoId);
    $stmt->execute();
    $result = $stmt->get_result();
    $dadosEleicao = $result->fetch_assoc();
    $stmt->close();

    if (!$dadosEleicao) {
        throw new Exception("Eleição não encontrada.");
    }

    // 2. BUSCAR CANDIDATOS E CONTAR VOTOS
    // Fazemos um LEFT JOIN com a tabela votos para contar quantas vezes o ID do candidato aparece
    $sqlCandidatos = "
        SELECT 
            a.nome,
            COUNT(v.id) as total_votos
        FROM candidatos c
        INNER JOIN alunos a ON c.aluno_ra = a.ra
        LEFT JOIN votos v ON (v.candidato_id = c.id AND v.eleicao_id = ?)
        WHERE c.eleicao_id = ?
        GROUP BY c.id, a.nome
        ORDER BY total_votos DESC, a.nome ASC
    ";

    $stmt = $conexao->prepare($sqlCandidatos);
    $stmt->bind_param("ii", $eleicaoId, $eleicaoId);
    $stmt->execute();
    $resultCandidatos = $stmt->get_result();

    $listaCandidatos = [];
    $somaVotos = 0;

    while ($row = $resultCandidatos->fetch_assoc()) {
        $votos = intval($row['total_votos']);
        $somaVotos += $votos;
        
        $listaCandidatos[] = [
            'nome' => $row['nome'],
            'votos' => $votos,
            'situacao' => 'Não Eleito' // Define padrão, alteraremos o primeiro depois
        ];
    }
    $stmt->close();

    // 3. LÓGICA DE QUEM FOI ELEITO
    // Se houver candidatos e votos, o primeiro da lista (que já está ordenada por votos DESC) é o eleito
    if (count($listaCandidatos) > 0) {
        // Verifica empate: se o primeiro tem os mesmos votos que o segundo
        if (count($listaCandidatos) > 1 && $listaCandidatos[0]['votos'] > 0 && $listaCandidatos[0]['votos'] == $listaCandidatos[1]['votos']) {
             $listaCandidatos[0]['situacao'] = 'Empate (Eleito*)'; // *Critério de desempate manual
             $listaCandidatos[1]['situacao'] = 'Empate';
        } elseif ($listaCandidatos[0]['votos'] > 0) {
             $listaCandidatos[0]['situacao'] = 'Eleito';
        } else {
            // Se ninguém teve votos
             $listaCandidatos[0]['situacao'] = '-';
        }
    }

    // 4. MONTAR O OBJETO FINAL
    $resposta = [
        'titulo' => $dadosEleicao['titulo'],
        'data_inicio' => $dadosEleicao['data_inicio'],
        'data_fim' => $dadosEleicao['data_fim'],
        'curso' => $dadosEleicao['nome_curso'] ?? 'Curso Geral / Não Identificado',
        'total_votos' => $somaVotos,
        'candidatos' => $listaCandidatos
    ];

    // Retorna array (pois o JS espera lista de projetos/eleições)
    echo json_encode([$resposta]);

    $conexao->close();

} catch (Exception $e) {
    // Em caso de erro, retorna JSON de erro mas com código 200 para o JS ler a mensagem, 
    // ou código 500 se preferir.
    http_response_code(500);
    echo json_encode(['erro' => $e->getMessage()]);
}
?>