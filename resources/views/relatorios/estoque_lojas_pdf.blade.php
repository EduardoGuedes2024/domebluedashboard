<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <style>
        /* Configuração de Margens para o Header e Footer fixos */
        @page { margin: 100px 25px 60px 25px; }

        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; margin: 0; padding: 0; }

        /* CABEÇALHO FIXO (Identidade igual à Movimentação) */
        .header { position: fixed; top: -80px; left: 0; right: 0; text-align: center; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; height: 70px; }
        .header h2 { margin: 0; font-size: 18px; color: #333; text-transform: uppercase; letter-spacing: 1px; }
        .header p { margin: 5px 0 0 0; font-size: 11px; color: #444; }

        /* RODAPÉ FIXO (Paginação) */
        .footer { position: fixed; bottom: -40px; left: 0; right: 0; height: 30px; text-align: center; font-size: 9px; color: #777; border-top: 1px solid #eee; padding-top: 5px; }

        /* CARD DO PRODUTO */
        .card { border: 1px solid #ddd; margin-bottom: 25px; page-break-inside: avoid; border-radius: 4px; overflow: hidden; }
        
        /* LINHA DE TÍTULO AZUL (Igual ao da Movimentação) */
        .card-header { background-color: #1e3a8a; color: white; padding: 8px 12px; font-weight: bold; font-size: 11px; }

        .row { display: table; width: 100%; }
        .col { display: table-cell; vertical-align: top; padding: 10px; }
        .col-img { width: 130px; border-right: 1px solid #eee; text-align: center; background-color: #fafafa; }
        
        .imgbox img { max-width: 120px; max-height: 160px; object-fit: contain; }

        /* INFORMAÇÕES E TABELA */
        .info-header { margin-bottom: 8px; }
        .precos { margin-top: 4px; font-size: 10px; color: #555; }
        .precos strong { color: #000; }

        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th { background-color: #f3f4f6; color: #444; font-size: 8px; text-transform: uppercase; padding: 5px; border: 1px solid #ccc; }
        td { padding: 5px; border: 1px solid #eee; text-align: center; font-size: 10px; }
        
        /* Alinhamento da Variação */
        .td-variacao { text-align: left; font-weight: bold; width: 35%; background-color: #fcfcfc; }
        .td-total { font-weight: 900; background-color: #f1f5f9; color: #1e3a8a; }

    </style>
</head>
<body>

    {{-- CABEÇALHO QUE SE REPETE --}}
    <div class="header">
        <h2>RELATÓRIO ESTOQUE - LOJA {{ strtoupper($localLabel) }}</h2>
        <p>Gerado em: {{ date('d/m/Y H:i') }} | Sistema <strong>DOME BLUE</strong></p>
    </div>

    {{-- RODAPÉ QUE SE REPETE --}}
    <div class="footer">
        DomeBlue Dashboard - Relatório Geral de Disponibilidade
    </div>

    {{-- LISTAGEM DE PRODUTOS --}}
    @foreach($cards as $c)
        @php $p = $c['produto']; @endphp
        <div class="card">
            {{-- Título do Card com Fundo Azul Sólido --}}
            <div class="card-header">
                REF: {{ $p->cod_produto_pai ?? '-' }} — {{ mb_convert_encoding($p->des_produto, 'UTF-8', 'ISO-8859-1') }}
            </div>

            <div class="row">
                {{-- Coluna da Imagem --}}
                <div class="col col-img">
                    <div class="imgbox">
                        @if(!empty($c['imgLocal']))
                            <img src="{{ $c['imgLocal'] }}" alt="foto">
                        @else
                            <div style="padding-top: 70px; color:#ccc; font-size: 8px;">SEM FOTO</div>
                        @endif
                    </div>
                </div>

                {{-- Coluna dos Dados --}}
                <div class="col">
                    <div class="precos">
                        <strong>Varejo:</strong> R$ {{ number_format((float)($p->preco_01 ?? 0), 2, ',', '.') }}
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        <strong>Atacado:</strong> R$ {{ number_format((float)($p->preco_02 ?? 0), 2, ',', '.') }}
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Variação</th>
                                @foreach($lojasFixas as $loja) 
                                    <th>{{ $loja }}</th> 
                                @endforeach
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($c['matriz'] as $m)
                                <tr>
                                    <td class="td-variacao">{{ $m['variacao'] }}</td>
                                    @foreach($lojasFixas as $loja)
                                        <td>{{ $m['lojas'][$loja] ?? 0 }}</td>
                                    @endforeach
                                    <td class="td-total">{{ $m['total'] ?? 0 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach

    {{-- SCRIPT PARA NUMERAÇÃO DE PÁGINAS (EXCLUSIVO DOMPDF) --}}
    <script type="text/php">
        if ( isset($pdf) ) {
            $font = $fontMetrics->get_font("DejaVu Sans", "normal");
            $size = 9;
            $y = $pdf->get_height() - 35;
            $x = $pdf->get_width() - 100;
            $pdf->page_text($x, $y, "Página {PAGE_NUM} de {PAGE_COUNT}", $font, $size, array(0,0,0));
        }
    </script>

</body>
</html>