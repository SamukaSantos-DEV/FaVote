<?php
header('Content-Type: application/json; charset=utf-8');
require 'config.php';

try {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception("ID da eleição não fornecido.");
    }
    $eleicaoId = intval($_GET['id']);

    // 1. DADOS DA ELEIÇÃO E TURMA
    $sqlEleicao = "
        SELECT e.titulo, e.data_inicio, e.data_fim, e.turma_id,
               c.nome AS nome_curso, t.semestre_id AS semestre_id_turma
        FROM eleicoes e
        LEFT JOIN turmas t ON e.turma_id = t.id
        LEFT JOIN cursos c ON t.curso_id = c.id
        WHERE e.id = ? LIMIT 1
    ";
    $stmt = $conexao->prepare($sqlEleicao);
    $stmt->bind_param("i", $eleicaoId);
    $stmt->execute();
    $dadosEleicao = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$dadosEleicao)
        throw new Exception("Eleição não encontrada.");

    $turmaId = $dadosEleicao['turma_id'];
    $nomeCurso = $dadosEleicao['nome_curso'] ?? 'Curso Não Identificado';
    $semestreId = $dadosEleicao['semestre_id_turma'] ?? 1;

    // 2. TOTAL DE ALUNOS MATRICULADOS (Universo de Votantes)
    // Exclui o aluno 'Branco' (ID 1) da contagem de pessoas reais
    $totalAlunosMatriculados = 0;
    if ($turmaId) {
        $sqlCount = "SELECT COUNT(*) as total FROM alunos WHERE turma_id = ? AND id != 1";
        $stmt = $conexao->prepare($sqlCount);
        $stmt->bind_param("i", $turmaId);
        $stmt->execute();
        $totalAlunosMatriculados = intval($stmt->get_result()->fetch_assoc()['total']);
        $stmt->close();
    }

    // 3. CONTAGEM DE VOTOS
    // Conta quantos votaram no total e quantos votaram especificamente no candidato ID 1 (Branco)
    $sqlVotos = "
        SELECT 
            COUNT(*) as total_comparecimento,
            SUM(CASE WHEN candidato_id = 1 THEN 1 ELSE 0 END) as votos_brancos_urna
        FROM votos 
        WHERE eleicao_id = ?
    ";
    $stmt = $conexao->prepare($sqlVotos);
    $stmt->bind_param("i", $eleicaoId);
    $stmt->execute();
    $resVotos = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $totalComparecimento = intval($resVotos['total_comparecimento']); // Quantos clicaram em votar
    $votosBrancosUrna = intval($resVotos['votos_brancos_urna']); // Votos no ID 1

    // 4. CÁLCULOS FINAIS
    // Abstenções = Total Matriculados - Quem Compareceu
    $abstencoes = max(0, $totalAlunosMatriculados - $totalComparecimento);

    // Total "Em Branco" para a Ata = (Votos no ID 1) + (Quem não votou)
    $totalVotosNaoValidos = $votosBrancosUrna + $abstencoes;

    // Votos Válidos = Comparecimento - Votos no ID 1
    $totalVotosValidos = max(0, $totalComparecimento - $votosBrancosUrna);

    // 5. LISTA DE CANDIDATOS VÁLIDOS (Exclui ID 1)
    $sqlCandidatos = "
        SELECT a.nome, a.ra, COUNT(v.id) as votos
        FROM candidatos c
        JOIN alunos a ON c.aluno_ra = a.ra
        LEFT JOIN votos v ON (v.candidato_id = c.id AND v.eleicao_id = ?)
        WHERE c.eleicao_id = ? AND c.id != 1
        GROUP BY c.id, a.nome, a.ra
        ORDER BY votos DESC, a.nome ASC
    ";
    $stmt = $conexao->prepare($sqlCandidatos);
    $stmt->bind_param("ii", $eleicaoId, $eleicaoId);
    $stmt->execute();
    $resCand = $stmt->get_result();

    $listaCandidatos = [];
    while ($row = $resCand->fetch_assoc()) {
        $listaCandidatos[] = [
            'nome' => $row['nome'],
            'ra' => $row['ra'],
            'votos' => intval($row['votos']),
            'situacao' => 'Não Eleito'
        ];
    }
    $stmt->close();

    // Define Eleito/Vice (apenas entre candidatos válidos)
    if (count($listaCandidatos) > 0) {
        $votosPrimeiro = $listaCandidatos[0]['votos'];
        if ($votosPrimeiro >= 0) { // Mesmo com 0, se for o único, pode ser eleito dependendo da regra
            // Verifica empate
            if (count($listaCandidatos) > 1 && $listaCandidatos[1]['votos'] == $votosPrimeiro && $votosPrimeiro > 0) {
                $listaCandidatos[0]['situacao'] = 'Empate';
                $listaCandidatos[1]['situacao'] = 'Empate';
            } else {
                $listaCandidatos[0]['situacao'] = 'Eleito';
                if (count($listaCandidatos) > 1) {
                    $listaCandidatos[1]['situacao'] = 'Vice Eleito';
                }
            }
        }
    }

    // 6. LISTA DE QUEM VOTOU (Assinaturas)
    $sqlPart = "SELECT DISTINCT a.nome, a.ra FROM votos v JOIN alunos a ON v.aluno_ra = a.ra WHERE v.eleicao_id = ? ORDER BY a.nome ASC";
    $stmt = $conexao->prepare($sqlPart);
    $stmt->bind_param("i", $eleicaoId);
    $stmt->execute();
    $resPart = $stmt->get_result();
    $participantes = [];
    while ($r = $resPart->fetch_assoc())
        $participantes[] = $r;
    $stmt->close();

    $resposta = [
        'titulo' => $dadosEleicao['titulo'],
        'data_inicio' => $dadosEleicao['data_inicio'],
        'data_fim' => $dadosEleicao['data_fim'],
        'curso' => $nomeCurso,
        'semestre_id_turma' => $semestreId,
        'total_alunos_matriculados' => $totalAlunosMatriculados,
        'total_votos_brancos_final' => $totalVotosNaoValidos,
        'total_votos_validos' => $totalVotosValidos,
        'candidatos' => $listaCandidatos,
        'participantes' => $participantes
    ];

    echo json_encode([$resposta]);
    $conexao->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => $e->getMessage()]);
}
?>