async function gerarAtaPDF(eleicaoId) {
    try {
        if (!eleicaoId) {
            const urlParams = new URLSearchParams(window.location.search);
            eleicaoId = urlParams.get('eleicao_id');
        }

        const response = await fetch(`../php/get_election_result.php?id=${eleicaoId}`);
        if (!response.ok) throw new Error(`Erro API: ${response.status}`);
        const text = await response.text();
        if (!text.trim()) { alert("Sem dados."); return; }

        let eleicoes;
        try { eleicoes = JSON.parse(text); } catch (e) { alert("Erro JSON"); return; }
        if (!Array.isArray(eleicoes)) eleicoes = [eleicoes];

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ unit: "mm", format: "a4" });
        const margin = 20, pageWidth = 210, maxTextWidth = 170, lineHeight = 7;

        // --- FUNÇÕES DE FORMATAÇÃO ---

        // Converte string para Título (Primeiras Letras Maiúsculas)
        function toTitleCase(str) {
            return str.replace(/\w\S*/g, function (txt) {
                // Não capitaliza preposições curtas se quiser (opcional)
                if (['de', 'e', 'do', 'da'].includes(txt.toLowerCase())) return txt.toLowerCase();
                return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
            });
        }

        function mapearCurso(n) {
            if (!n) return "CURSO NÃO INFORMADO";
            const u = n.toUpperCase().trim();
            if (u.includes('DSM') || u.includes('SOFTWARE')) return 'DESENVOLVIMENTO DE SOFTWARE MULTIPLATAFORMA';
            if (u.includes('GPI') || u.includes('PRODUÇÃO')) return 'GESTÃO DE PRODUÇÃO INDUSTRIAL';
            if (u.includes('GE') || u.includes('EMPRESARIAL')) return 'GESTÃO EMPRESARIAL';
            return u.replace(' DE TECNOLOGIA', '');
        }

        function getPeriodoExtenso(id) {
            return { 1: 'PRIMEIRO', 2: 'SEGUNDO', 3: 'TERCEIRO', 4: 'QUARTO', 5: 'QUINTO', 6: 'SEXTO' }[parseInt(id)] || '_______';
        }

        function getSemestreNum(d) {
            const dt = new Date(d);
            return (isNaN(dt.getTime()) || dt.getMonth() + 1 < 7) ? "PRIMEIRO" : "SEGUNDO";
        }

        function numExtenso(n) {
            n = parseInt(n);
            const u = ["", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove"];
            const d = ["", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa"];
            const dc = ["dez", "onze", "doze", "treze", "catorze", "quinze", "dezesseis", "dezessete", "dezoito", "dezenove"];
            const c = ["", "cento", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos"];
            if (n == 0) return "zero";
            if (n < 10) return u[n];
            if (n < 20) return dc[n - 10];
            if (n < 100) { let r = n % 10; return d[Math.floor(n / 10)] + (r ? " e " + u[r] : ""); }
            if (n == 100) return "cem";
            if (n < 1000) { let r = n % 100; return c[Math.floor(n / 100)] + (r ? " e " + numExtenso(r) : ""); }
            if (n < 1000000) {
                let r = n % 1000, m = Math.floor(n / 1000);
                let milhar = (m == 1 ? "mil" : numExtenso(m) + " mil");
                if (r == 0) return milhar;
                if (r < 100 || r % 100 == 0) return milhar + " e " + numExtenso(r);
                return milhar + ", " + numExtenso(r);
            }
            return n.toString();
        }

        function getDataExtensoCompleta(iso, incluirCidade = false) {
            const d = new Date(iso); d.setMinutes(d.getMinutes() + d.getTimezoneOffset());
            if (isNaN(d.getTime())) return "Data desconhecida";

            const dia = d.getDate(), ano = d.getFullYear();
            const meses = ["janeiro", "fevereiro", "março", "abril", "maio", "junho", "julho", "agosto", "setembro", "outubro", "novembro", "dezembro"];
            const ord = ["", "primeiro", "segundo", "terceiro", "quarto", "quinto", "sexto", "sétimo", "oitavo", "nono", "décimo"];

            let diaTxt = "";
            if (dia <= 10) diaTxt = ord[dia];
            else if (dia == 20) diaTxt = "vigésimo";
            else if (dia == 30) diaTxt = "trigésimo";
            else diaTxt = numExtenso(dia);

            let txt = `${diaTxt} dia do mês de ${meses[d.getMonth()]} de ${numExtenso(ano)}`;

            if (incluirCidade) {
                // Formato final: Itapira, Vinte e Cinco de Novembro...
                return `\nItapira, ${toTitleCase(diaTxt)} de ${toTitleCase(meses[d.getMonth()])} de ${toTitleCase(numExtenso(ano))}.`;
            }
            return `Ao ${diaTxt} dia do mês de ${meses[d.getMonth()]} de ${numExtenso(ano)}`;
        }

        eleicoes.forEach((dados, idx) => {
            if (idx > 0) doc.addPage();
            let y = 20;

            // --- IMAGENS (Correção de proporção) ---
            try {
                const imgFatec = new Image(); imgFatec.src = '../Images/logofatec.png';
                // Largura 0 = Automática proporcional à altura de 15mm
                doc.addImage(imgFatec, 'PNG', margin, 10, 0, 15);

                const imgSp = new Image(); imgSp.src = '../Images/images (1).png';
                // Largura 0 = Automática proporcional à altura de 20mm
                doc.addImage(imgSp, 'PNG', 170, 8, 0, 20);
            } catch (e) { }
            y = 45;

            // --- DADOS ---
            const cands = dados.candidatos || [];
            // O PHP já exclui o ID 1, mas filtramos por segurança
            const candsValidos = cands.filter(c => !c.nome.toLowerCase().includes("branco"));

            const totalMatriculados = parseInt(dados.total_alunos_matriculados || 0);
            const totalBrancos = parseInt(dados.total_votos_brancos_final || 0);
            const totalValidos = parseInt(dados.total_votos_validos || 0);

            // Se tem votos válidos, define eleitos
            const houveEleitos = totalValidos > 0 && candsValidos.length > 0 && candsValidos[0].votos > 0;
            const rep = houveEleitos ? candsValidos[0] : null;
            const vice = (houveEleitos && candsValidos.length > 1) ? candsValidos[1] : null;

            const curso = mapearCurso(dados.curso);
            const periodo = getPeriodoExtenso(dados.semestre_id_turma || 1);
            const dataRef = dados.data_fim || new Date().toISOString();
            const anoDoc = new Date(dataRef).getFullYear();
            const semestreTxt = getSemestreNum(dataRef);

            // --- TÍTULO ---
            doc.setFont("times", "bold"); doc.setFontSize(12);
            const titulo = `ATA DE ELEIÇÃO DE REPRESENTANTES DE TURMA DO ${periodo} PERÍODO DO ${semestreTxt} SEMESTRE DE ${numExtenso(anoDoc).toUpperCase()}, DO CURSO DE TECNOLOGIA EM ${curso} DA FACULDADE DE TECNOLOGIA DE ITAPIRA “OGARI DE CASTRO PACHECO”.`;
            const splitTitulo = doc.splitTextToSize(titulo, maxTextWidth);
            doc.text(splitTitulo, 105, y, { align: "center" });
            y += (splitTitulo.length * 6) + 10;

            // --- TEXTO ---
            doc.setFont("times", "normal"); doc.setFontSize(12);

            let texto = `${getDataExtensoCompleta(dataRef)}, foram apurados os votos dos alunos regularmente matriculados no ${periodo.toLowerCase()} período do ${semestreTxt.toLowerCase()} semestre de ${numExtenso(anoDoc)} do Curso Superior de Tecnologia em ${curso} para eleição de novos representantes de turma. `;

            texto += `Os representantes eleitos fazem a representação dos alunos nos órgãos colegiados da Faculdade, com direito a voz e voto, conforme o disposto no artigo 69 da Deliberação CEETEPS nº 07, de 15 de dezembro de 2006. `;

            if (houveEleitos) {
                texto += `Foi eleito(a) como representante o(a) aluno(a) ${rep.nome.toUpperCase()} com ${rep.votos} votos (RA: ${rep.ra || 'N/A'})`;
                if (vice) {
                    texto += ` e eleito como vice o(a) aluno(a) ${vice.nome.toUpperCase()} com ${vice.votos} votos (RA: ${vice.ra || 'N/A'}). `;
                } else {
                    texto += `. O cargo de vice-representante permaneceu vago. `;
                }
            } else {
                texto += `Não houve votos válidos computados, portanto os cargos permaneceram vagos. `;
            }

            texto += `De um total de ${totalMatriculados} alunos matriculados aptos a votar, ${totalBrancos} votos foram contabilizados como brancos, nulos ou abstenções. `;
            texto += `A presente ata, após leitura e concordância, vai assinada por todos os alunos participantes.`;

            // ADICIONA DATA E LOCAL NO FINAL, CAPITALIZADA
            texto += `${getDataExtensoCompleta(dataRef, true)}`;

            const splitTexto = doc.splitTextToSize(texto, maxTextWidth);
            doc.text(splitTexto, margin, y, { align: "justify", maxWidth: maxTextWidth });
            y += (splitTexto.length * lineHeight) + 15;

            // --- TABELA ---
            if (y > 220) { doc.addPage(); y = margin; }
            doc.setFont("times", "bold"); doc.setFontSize(10);
            const cols = [10, 85, 35, 40];
            let x = margin;
            doc.setLineWidth(0.1);

            const headers = ["Nº", "NOME", "R.A.", "ASSINATURA"];
            headers.forEach((h, i) => {
                doc.rect(x, y, cols[i], 8);
                doc.text(h, x + cols[i] / 2, y + 5.5, { align: "center" });
                x += cols[i];
            });
            y += 8;

            doc.setFont("times", "normal");
            const parts = dados.participantes || [];
            const linhas = Math.max(parts.length, 15);

            for (let i = 0; i < linhas; i++) {
                if (y > 275) { doc.addPage(); y = margin; doc.line(margin, y, margin + 170, y); }
                let px = margin;
                const p = parts[i] || { nome: "", ra: "" };

                doc.rect(px, y, cols[0], 8);
                doc.text((i + 1).toString(), px + cols[0] / 2, y + 5.5, { align: "center" }); px += cols[0];

                doc.rect(px, y, cols[1], 8);
                let nm = p.nome ? p.nome.toUpperCase() : "";
                if (nm.length > 40) nm = nm.substring(0, 37) + "...";
                doc.text(nm, px + 2, y + 5.5); px += cols[1];

                doc.rect(px, y, cols[2], 8);
                doc.text(p.ra || "", px + cols[2] / 2, y + 5.5, { align: "center" }); px += cols[2];

                doc.rect(px, y, cols[3], 8);
                y += 8;
            }

            doc.setFontSize(8); doc.setTextColor(150);
            doc.text("Documento gerado pelo sistema FaVote.", 105, 290, { align: "center" });
            doc.setTextColor(0);
        });

        doc.save(eleicaoId ? `Ata_Eleicao_${eleicaoId}.pdf` : `Ata_Eleicao.pdf`);
    } catch (err) { console.error(err); alert("Erro PDF: " + err.message); }
}