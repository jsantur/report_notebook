<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Turno - {{ $reporte->fecha }}</title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 10pt;
            line-height: 1.2;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
                .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 0;
            border-bottom: none;
        }
        .header img {
            height: 140px;
            width: auto;
            margin-bottom: 0;
            display: inline-block;
        }
        .header .title-container {
            margin-top: -10px;
        }
        .header h1 {
            margin: 0;
            font-size: 32pt;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: -1px;
            line-height: 0.9;
        }
        .header .shift-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 5px;
        }
        .header .shift-divider::before,
        .header .shift-divider::after {
            content: "";
            flex: 1;
            height: 3px;
            background-color: #1e40af;
        }
        .header .shift-name {
            padding: 0 20px;
            font-size: 16pt;
            font-weight: 800;
            color: #1e40af;
            letter-spacing: 5px;
            text-transform: uppercase;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 20px;
            background-color: #f8fafc;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .info-item b {
            color: #64748b;
            font-size: 8pt;
            text-transform: uppercase;
            display: block;
        }
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            background-color: #f1f5f9;
            padding: 5px 10px;
            margin: 15px 0 10px 0;
            border-left: 4px solid #3b82f6;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f8fafc;
            font-size: 9pt;
            font-weight: bold;
            color: #475569;
        }
        .table-compact th, .table-compact td {
            padding: 4px 6px;
            font-size: 9pt;
        }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-blue { color: #2563eb; }
        
        /* Estilos específicos para el Historial Optimizado */
        .historial-table th {
            background-color: #1e293b;
            color: white;
            text-align: center;
        }
        .historial-table td {
            text-align: center;
            font-size: 8.5pt;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8pt;
            color: #94a3b8;
        }
        
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
            .section-title { -webkit-print-color-adjust: exact; }
        }
        
        .btn-print {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #2563eb;
            color: white;
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <a href="javascript:window.print()" class="btn-print no-print">Imprimir Reporte</a>

    @php
        $logoPath = public_path('img/logo_reportePDF.png');
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoSrc = 'data:image/png;base64,' . $logoData;
        } else {
            $logoSrc = '';
        }
    @endphp
    <div class="header">
        @if($logoSrc)
            <img src="{{ $logoSrc }}" alt="Escudo">
        @endif
        <div class="title-container">
            <h1>Reporte de Turno</h1>
            <div class="shift-divider">
                <span class="shift-name">{{ $reporte->turno }}</span>
            </div>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-item"><b>Responsable del Cuaderno:</b> {{ strtoupper(Auth::user()->name ?? 'N/A') }}</div>
        <div class="info-item"><b>Fecha:</b> {{ $reporte->fecha }}</div>
        <div class="info-item"><b>Hora Registro:</b> {{ $reporte->hora }}</div>
        <div class="info-item"><b>Supervisor de Campo:</b> {{ $reporte->supervisorCampo->nombres ?? 'N/A' }} {{ $reporte->supervisorCampo->apellido_paterno ?? '' }}</div>
        <div class="info-item"><b>Supervisor de Cámaras:</b> 
            @php
                $sups = $reporte->supervisores_camaras_list;
            @endphp
            {{ $sups->map(fn($s) => $s->nombres . ' ' . $s->apellido_paterno)->join(' / ') ?: 'N/A' }}
        </div>
    </div>

    <div class="section-title">🔄 Ocurrencias de Relevo</div>
    <div style="white-space: pre-wrap; padding: 0 10px; margin-bottom: 20px; border: 1px solid #f1f5f9; border-radius: 8px; padding: 10px; background: white;">{{ $reporte->ocurrencias_relevo ?: 'Sin novedades registradas.' }}</div>

    <div class="section-title">👥 Personal de Cámaras</div>
    <table class="table-compact">
        <thead>
            <tr>
                <th style="width: 30%;">Nombre</th>
                <th style="width: 15%;">Máquina</th>
                <th style="width: 55%;">Cámaras Asignadas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($distribucionCamaras as $op)
            <tr>
                <td>{{ $op['nombres'] }} {{ $op['apellido_paterno'] }}</td>
                <td class="font-bold">{{ $op['maquina'] }}</td>
                <td style="font-size: 7.5pt; color: #475569; line-height: 1.1;">
                    @if(!empty($op['camaras']))
                        {{ is_array($op['camaras']) ? implode(', ', $op['camaras']) : $op['camaras'] }}
                    @else
                        <span style="color: #cbd5e1;">Sin cámaras asignadas</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">🚓 Distribución de Personal de Campo</div>
    <table class="table-compact">
        <thead>
            <tr>
                <th>Unidad</th>
                <th>Placa</th>
                <th>Personal</th>
                <th>Tipo</th>
                <th>Ubicación</th>
                <th>Códigos</th>
            </tr>
        </thead>
        <tbody>
                        @foreach($distribucionCampo as $item)
            <tr>
                <td class="font-bold">{{ $item['unidad'] ?? 'N/A' }}</td>
                <td>{{ $item['matricula'] ?? 'N/A' }}</td>
                <td>
                    @if($item['tipo_patrullaje'] === 'Vehicular')
                        Chofer: {{ $item['chofer'] }} / Op: {{ $item['operador'] }}
                        @if(!empty($item['lince'])) / Lince: {{ $item['lince'] }} @endif
                    @elseif($item['tipo_patrullaje'] === 'Motorizado')
                        Chofer: {{ $item['chofer'] }}
                    @else
                        Sereno: {{ $item['sereno'] }}
                    @endif
                    @if(!empty($item['patrullaje_integrado']))
                        <div style="margin-top: 4px; font-size: 7.5pt; color: #475569;">
                            <strong>Integrado:</strong> 
                            @foreach($item['patrullaje_integrado'] as $pnp)
                                {{ $pnp['grado'] ?? '' }} {{ $pnp['nombre'] ?? '' }} {{ $pnp['apellidos'] ?? '' }}
                                @if(!empty($pnp['imei'])) [IMEI: {{ $pnp['imei'] }}] @endif
                                @if(!$loop->last) , @endif
                            @endforeach
                        </div>
                    @endif
                </td>
                <td>{{ $item['tipo_patrullaje'] }}</td>
                <td>{{ $item['ubicacion'] }}</td>
                <td class="font-bold text-blue">{{ $item['cod_po'] ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if(!empty($halconReportes))
    <div class="section-title">🕓 Historial de Reportes (Optimizado)</div>
    @php
        // Obtener todas las unidades únicas que aparecen en los reportes
        $unidadesUnicas = [];
        foreach($halconReportes as $rep) {
            if(isset($rep['rawData']) && is_array($rep['rawData'])) {
                foreach($rep['rawData'] as $u) {
                    if(!in_array($u['unidad'], $unidadesUnicas)) {
                        $unidadesUnicas[] = $u['unidad'];
                    }
                }
            }
        }
        sort($unidadesUnicas);
    @endphp
    <table class="historial-table table-compact">
        <thead>
            <tr>
                <th style="width: 80px;">Hora</th>
                @foreach($unidadesUnicas as $unit)
                <th>{{ str_contains($unit, ' ') ? $unit : 'Unidad ' . $unit }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($halconReportes as $rep)
            <tr>
                <td class="font-bold">{{ $rep['hora'] }}</td>
                @foreach($unidadesUnicas as $unit)
                <td>
                    @php
                        $status = '';
                        if(isset($rep['rawData']) && is_array($rep['rawData'])) {
                            $uData = collect($rep['rawData'])->firstWhere('unidad', $unit);
                            if($uData) {
                                $status = $uData['observacion'] ?: 'OK';
                                if(stripos($status, 'no responde') !== false || stripos($status, 'no contesto') !== false) {
                                    $status = '❌';
                                }
                            } else {
                                $status = '-';
                            }
                        }
                    @endphp
                    {{ $status }}
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="font-size: 7pt; margin-bottom: 15px; color: #64748b;">
        * ❌ = Sin respuesta | OK = Sin novedad | - = No asignado en ese reporte
    </div>
    @endif

    <div class="section-title">⚠️ Visualizaciones Resaltantes (IA)</div>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px;">
        @foreach($visualizacionesIA as $vis)
        <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px; background: white;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                <b style="font-size: 8pt; color: #1e293b;">{{ $vis['camara'] }}</b>
                <span style="font-size: 7pt; color: #64748b;">{{ $vis['hora'] }}</span>
            </div>
            <p style="margin: 0; font-size: 8.5pt; font-style: italic; color: #475569;">{{ $vis['corregido'] ?: $vis['original'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="section-title">🚗 Kilometraje de Unidades</div>
    <table class="table-compact">
        <thead>
            <tr>
                <th>Unidad</th>
                <th>Placa</th>
                <th class="text-center">KM Final</th>
                <th class="text-center">Recorrido A/P</th>
                <th class="text-center">Total P/O</th>
                <th>Códigos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reporte->asignaciones as $asig)
            <tr>
                <td class="font-bold">{{ $asig->unidad_id }}</td>
                <td>{{ $asig->placa }}</td>
                <td class="text-center">{{ $asig->km }}</td>
                <td class="text-center">{{ $asig->ap }}</td>
                <td class="text-center">{{ $asig->po }}</td>
                <td>{{ $asig->cod_po ?: '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Este documento es un reporte oficial generado por el sistema de Seguridad Ciudadana.<br>
        Fecha de impresión: {{ date('Y-m-d H:i:s') }}
    </div>

    <script>
        window.onload = function() {
            // setTimeout(() => { window.print(); }, 500);
        };
    </script>
</body>
</html>
