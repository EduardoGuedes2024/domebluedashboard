<html>
<head>
    <style>
        
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { bg-color: #f2f2f2; font-weight: bold; text-transform: uppercase; }
        .header { text-align: center; margin-bottom: 20px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>DOMEBLUE - RELATÓRIO DE GIRO DE ESTOQUE</h2>
        <p>LOJA: {{ strtoupper($unidade) }} | PERÍODO: {{ $periodo }} DIAS | GRUPO: {{ $grupo ?? 'TODOS' }}</p>
    </div>

    <table>
        <thead>

            <tr>

                <th>Referência</th>

                <th>Produto / Variação</th>

                <th>Saldo</th>

                <th>Preço</th>

                <th>status</th>

            </tr>

        </thead>
        
        <tbody>
            @foreach($produtos as $p)
            <tr>
                <td><strong>{{ $p->cod_produto_pai }}</strong></td>

                <td>
                    <strong>{{ mb_convert_encoding($p->des_produto, 'UTF-8', 'ISO-8859-1') }}</strong><br>
                    <small>{{ mb_convert_encoding($p->des1_produto, 'UTF-8', 'ISO-8859-1') }}</small>
                </td>

                <td>{{ (int)$p->saldo }} UN</td>

                <td>R$ {{ number_format($p->preco, 2, ',', '.') }}</td>

                <td class="td-status">
                    @if($periodo <= 30)
                        {{-- Lógica para Giro Rápido --}}
                        <span style="color: #28a745; font-weight: bold;">
                            MOVIMENTADO NOS ÚLTIMOS <br>{{ $periodo }} DIAS
                        </span>
                    @else
                        {{-- Lógica para Giro Lento (60, 90, 120, 150) --}}
                        <span style="color: #dc3545; font-weight: bold;">
                            ÚLTIMO MOVIMENTO A + DE <br>{{ $periodo }} DIAS
                        </span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Gerado em {{ date('d/m/Y H:i') }}</div>
</body>
</html>