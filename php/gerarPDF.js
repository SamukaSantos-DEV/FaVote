async function gerarAtaPDF(eleicaoId) {

    try {

        // 🔥 CAMINHO CORRIGIDO
        const response = await fetch(`../php/get_election_result.php?id=${eleicaoId}`);

        if (!response.ok) {
            throw new Error(`Erro HTTP: ${response.status}`);
        }

        const text = await response.text();

        if (!text.trim()) {
            alert("Nenhum dado retornado pela API.");
            return;
        }

        let eleicoes;
        try {
            eleicoes = JSON.parse(text);
        } catch (e) {
            console.error("Erro ao converter JSON:", text);
            alert("Resposta inválida do servidor.");
            return;
        }

        if (!Array.isArray(eleicoes)) {
            eleicoes = [eleicoes];
        }

        const jsPDF = window.jspdf.jsPDF;
        const doc = new jsPDF();

        let y = 50;

        /** Funções auxiliares **/
        const formatarData = (dataString) => {
            if (!dataString) return "Data desconhecida";
            const data = new Date(dataString);
            return data.toLocaleDateString('pt-BR', {
                day: '2-digit',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        };

        function addHeader() {
            doc.setDrawColor(178, 34, 34);
            doc.setFillColor(178, 34, 34);
            doc.rect(0, 0, 210, 35, 'F');

            // LOGO FAVOTE
            try {
                const favoteLogo = new Image();
                favoteLogo.src = '../Images/logoFaVote.png'; // 🔥 CAMINHO AJUSTADO
                doc.addImage(favoteLogo, 'PNG', 10, 3, 30, 30);
            } catch (e) {
                console.warn("Erro logo FaVote:", e);
            }

            // LOGO FATEC
            try {
                const fatecLogo = new Image();
                fatecLogo.src = '../Images/logofatec.png'; // 🔥 CAMINHO AJUSTADO
                doc.addImage(fatecLogo, 'PNG', 162, 6, 43, 21);
            } catch (e) {
                console.warn("Erro logo Fatec:", e);
            }

            doc.setTextColor(255, 255, 255);
            doc.setFontSize(18);
            doc.setFont('helvetica', 'bold');
            doc.text('ATA DE APURAÇÃO DE ELEIÇÃO', 105, 18, { align: 'center' });

            doc.setFontSize(12);
            doc.text('SISTEMA FAVOTE - FATEC', 105, 28, { align: 'center' });

            doc.setTextColor(0, 0, 0);
        }

        function addFooter() {
            doc.setFontSize(8);
            doc.setTextColor(100, 100, 100);
            const hoje = new Date().toLocaleDateString('pt-BR');
            doc.text(`Documento gerado automaticamente em ${hoje} pelo sistema FaVote`, 105, 285, { align: 'center' });
        }

        /** Geração das páginas **/
        eleicoes.forEach((dados, index) => {
            if (index > 0) doc.addPage();

            y = 50;
            addHeader();

            doc.setFontSize(14);
            doc.setFont('helvetica', 'bold');

            const titulo = doc.splitTextToSize(dados.titulo.toUpperCase(), 170);
            doc.text(titulo, 105, y, { align: 'center' });
            y += titulo.length * 7 + 10;

            const dataInicioFmt = formatarData(dados.data_inicio);
            const dataFimFmt = formatarData(dados.data_fim);

            const textoIntro = `Entre ${dataInicioFmt} e ${dataFimFmt}, ocorreu a votação para ${dados.titulo}, referente ao curso de ${dados.curso}. Após o encerramento, o sistema gerou automaticamente o resultado:`;

            const introSplit = doc.splitTextToSize(textoIntro, 170);
            doc.setFontSize(11);
            doc.text(introSplit, 20, y);
            y += introSplit.length * 6 + 15;

            // QUADRO DE TOTALIZAÇÃO
            doc.setFillColor(240, 240, 240);
            doc.rect(20, y, 170, 20, 'F');
            doc.setFont('helvetica', 'bold');
            doc.text(`Total de votos: ${dados.total_votos}`, 105, y + 13, { align: 'center' });
            y += 35;

            // TABELA
            doc.setFontSize(12);
            doc.text("DETALHAMENTO DA APURAÇÃO", 20, y);
            doc.line(20, y + 2, 190, y + 2);
            y += 10;

            doc.setFillColor(178, 34, 34);
            doc.setTextColor(255, 255, 255);
            doc.rect(20, y, 170, 8, 'F');
            doc.text("CANDIDATO", 25, y + 5);
            doc.text("VOTOS", 130, y + 5);
            doc.text("SITUAÇÃO", 160, y + 5);

            y += 10;
            doc.setTextColor(0, 0, 0);

            dados.candidatos.forEach((candidato, i) => {
                if (i % 2 === 0) {
                    doc.setFillColor(245, 245, 245);
                    doc.rect(20, y - 3, 170, 8, 'F');
                }
                doc.text(candidato.nome.toUpperCase(), 25, y);
                doc.text(String(candidato.votos), 135, y, { align: 'center' });

                if (candidato.situacao === "Eleito") {
                    doc.setFont('helvetica', 'bold');
                }

                doc.text(candidato.situacao, 160, y);
                doc.setFont('helvetica', 'normal');

                y += 8;
            });

            y += 20;

            const textoFinal = "Nada mais havendo a tratar, lavrou-se esta ata digital e automática, validada conforme procedimentos internos da unidade.";
            const textoFinalSplit = doc.splitTextToSize(textoFinal, 170);
            doc.text(textoFinalSplit, 20, y);

            addFooter();
        });

        doc.save(`Ata_Eleicao_${eleicaoId}.pdf`);

    } catch (err) {
        console.error(err);
        alert("Erro ao gerar ata: " + err.message);
    }
}
