<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; }
        .card { border: 1px solid #ccc; margin-bottom: 20px; page-break-inside: avoid; }
        .card-header { background: #1e3a8a; color: white; padding: 8px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f3f4f6; padding: 6px; border-bottom: 1px solid #ccc; text-align: left; text-transform: uppercase; font-size: 9px; }
        td { padding: 6px; border-bottom: 1px solid #eee; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-black { font-weight: bold; }
        .entrada { color: #15803d; }
        .saida { color: #b91c1c; }
        .footer-total { background: #f9fafb; font-weight: bold; border-top: 2px solid #333; }
    </style>
</head>
<body>
    <div class="header">
        <h2> MOVIMENTAÇÃO PRODUTO - LOJA {{ strtoupper($nomeLoja) }}</h2>
        <p>Codigo Pai: <strong>{{ $codigoPai }}</strong> | Gerado em: {{ date('d/m/Y H:i') }}</p>
    </div>

    @foreach($grupos as $tamanho => $historico)
        @php 
        // Pegamos o primeiro registro do grupo para exibir os dados do produto
        $primeiro = $historico->first(); 
        @endphp

        <div class="card">
            
            <div class="card-header">
                PRODUTO: {{ $primeiro->cod_produto_pai }} -
                {{ mb_convert_encoding($primeiro->des1_produto, 'UTF-8', 'ISO-8859-1') }} 
                ({{ $tamanho }})
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Data/Hora</th>
                        <th>Tipo</th>
                        <th>Documento</th>
                        <th class="text-center">Qtd</th>
                        <th>Observação</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historico as $m)
                    <tr>
                        <td>{{ date('d/m/Y', strtotime($m->data_responsavel)) }} {{ $m->hora_responsavel }}</td>
                        <td class="{{ $m->tipo == 'E' ? 'entrada' : 'saida' }}">
                            {{ $m->tipo == 'E' ? 'ENTRADA' : 'SAÍDA' }}
                        </td>
                        <td>{{ $m->documento_origem }}</td>
                        <td class="text-center font-black {{ $m->quantidade < 0 ? 'saida' : 'entrada' }}">
                            {{ $m->quantidade > 0 ? '+' : '' }}{{ (int)$m->quantidade }}
                        </td>
                        <td style="font-size: 8px; font-style: italic;">{{ $m->observacao }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="footer-total">
                    <tr>
                        <td colspan="3" class="text-right">SALDO LÍQUIDO DO PERÍODO:</td>
                        <td class="text-center" style="font-size: 14px;">
                            {{ $historico->sum('quantidade') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endforeach


</body>
</html>