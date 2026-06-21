<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <!-- Encabezado Principal -->
    <table>
        <!-- Fila de espacio para el logo (WithDrawings lo inserta en C1) -->
        <tr>
            <td colspan="6" style="height: 85px; text-align: center;"></td>
        </tr>
        <tr>
            <td colspan="6" style="background-color: #1e3a8a; color: #ffffff; font-size: 16pt; font-weight: bold; text-align: center; height: 40px;">
                REPORTE DE TURNO - {{ strtoupper($reporte->turno) }}
            </td>
        </tr>
        <tr>
            <td colspan="6" style="background-color: #3b82f6; color: #ffffff; font-size: 10pt; text-align: center; font-weight: bold;">
                SISTEMA DE SEGURIDAD CIUDADANA
            </td>
        </tr>
        <tr><td colspan="6"></td></tr> <!-- Espacio -->

        <!-- Información General -->
        <tr>
            <td style="font-weight: bold; background-color: #f1f5f9; border: 1px solid #cbd5e1;">Fecha:</td>
            <td style="border: 1px solid #cbd5e1;">{{ $reporte->fecha }}</td>
            <td style="font-weight: bold; background-color: #f1f5f9; border: 1px solid #cbd5e1;">Hora Registro:</td>
            <td style="border: 1px solid #cbd5e1;">{{ $reporte->hora }}</td>
            <td style="font-weight: bold; background-color: #f1f5f9; border: 1px solid #cbd5e1;">Responsable:</td>
            <td style="border: 1px solid #cbd5e1;">{{ strtoupper(auth()->user()->name ?? 'N/A') }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; background-color: #f1f5f9; border: 1px solid #cbd5e1;">Supervisor Campo:</td>
            <td colspan="2" style="border: 1px solid #cbd5e1;">{{ $reporte->supervisorCampo->nombres ?? 'N/A' }} {{ $reporte->supervisorCampo->apellido_paterno ?? '' }} {{ $reporte->supervisorCampo->apellido_materno ?? '' }}</td>
            <td style="font-weight: bold; background-color: #f1f5f9; border: 1px solid #cbd5e1;">Supervisor Cámaras:</td>
            <td colspan="2" style="border: 1px solid #cbd5e1;">
                @php
                    $sups = $reporte->supervisores_camaras_list;
                @endphp
                {{ $sups->map(fn($s) => $s->nombres . ' ' . $s->apellido_paterno . ' ' . $s->apellido_materno)->join(' / ') ?: 'N/A' }}
            </td>
        </tr>
        <tr><td colspan="6"></td></tr> <!-- Espacio -->

        <!-- Ocurrencias de Relevo -->
        <tr>
            <td colspan="6" style="background-color: #0f172a; color: #ffffff; font-size: 12pt; font-weight: bold; height: 30px;">
                🔄 OCURRENCIAS DE RELEVO
            </td>
        </tr>
        <tr>
            <td colspan="6" style="border: 1px solid #cbd5e1; font-style: italic; background-color: #fafafa; padding: 10px; height: 60px; vertical-align: top;">
                {{ $reporte->ocurrencias_relevo ?: 'Sin novedades registradas.' }}
            </td>
        </tr>
        <tr><td colspan="6"></td></tr> <!-- Espacio -->

        <!-- Personal de Cámaras -->
        <tr>
            <td colspan="6" style="background-color: #0f172a; color: #ffffff; font-size: 12pt; font-weight: bold; height: 30px;">
                👥 PERSONAL DE CÁMARAS
            </td>
        </tr>
        <tr style="background-color: #e2e8f0; font-weight: bold;">
            <td colspan="2" style="border: 1px solid #cbd5e1;">Operador</td>
            <td style="border: 1px solid #cbd5e1; text-align: center;">Máquina</td>
            <td colspan="3" style="border: 1px solid #cbd5e1;">Cámaras Asignadas</td>
        </tr>
        @if(!empty($distribucionCamaras))
            @foreach($distribucionCamaras as $op)
            <tr>
                <td colspan="2" style="border: 1px solid #cbd5e1;">{{ $op['nombres'] ?? '' }} {{ $op['apellido_paterno'] ?? '' }} {{ $op['apellido_materno'] ?? '' }}</td>
                <td style="border: 1px solid #cbd5e1; text-align: center; font-weight: bold; color: #1e3a8a;">{{ $op['maquina'] ?? '' }}</td>
                <td colspan="3" style="border: 1px solid #cbd5e1; font-size: 9pt; color: #475569;">
                    {{ !empty($op['camaras']) ? (is_array($op['camaras']) ? implode(', ', $op['camaras']) : $op['camaras']) : 'Sin cámaras asignadas' }}
                </td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colspan="6" style="border: 1px solid #cbd5e1; text-align: center; color: #94a3b8;">No hay personal de cámaras registrado.</td>
            </tr>
        @endif
        <tr><td colspan="6"></td></tr> <!-- Espacio -->

        <!-- Distribución de Personal de Campo -->
        <tr>
            <td colspan="6" style="background-color: #0f172a; color: #ffffff; font-size: 12pt; font-weight: bold; height: 30px;">
                🚓 DISTRIBUCIÓN DE PERSONAL DE CAMPO
            </td>
        </tr>
        <tr style="background-color: #e2e8f0; font-weight: bold;">
            <td style="border: 1px solid #cbd5e1;">Unidad</td>
            <td style="border: 1px solid #cbd5e1;">Placa</td>
            <td colspan="2" style="border: 1px solid #cbd5e1;">Personal Asignado</td>
            <td style="border: 1px solid #cbd5e1;">Modalidad</td>
            <td style="border: 1px solid #cbd5e1;">Zona / Sector</td>
        </tr>
        @if(!empty($distribucionCampo))
            @foreach($distribucionCampo as $item)
            <tr>
                <td style="border: 1px solid #cbd5e1; font-weight: bold; color: #1e3a8a;">{{ $item['unidad'] ?? 'N/A' }}</td>
                <td style="border: 1px solid #cbd5e1;">{{ $item['matricula'] ?? 'N/A' }}</td>
                <td colspan="2" style="border: 1px solid #cbd5e1;">
                    @if($item['tipo_patrullaje'] === 'Vehicular')
                        Chofer: {{ $item['chofer'] }} | Op: {{ $item['operador'] }}
                        @if(!empty($item['lince'])) | Lince: {{ $item['lince'] }} @endif
                    @elseif($item['tipo_patrullaje'] === 'Motorizado')
                        Chofer: {{ $item['chofer'] }}
                    @else
                        Sereno: {{ $item['sereno'] }}
                    @endif
                    @if(!empty($item['patrullaje_integrado']))
                        <br><span style="font-size: 8.5pt; color: #475569; font-weight: bold;">PNP:</span>
                        @foreach($item['patrullaje_integrado'] as $pnp)
                            {{ $pnp['grado'] ?? '' }} {{ $pnp['nombre'] ?? '' }} {{ $pnp['apellidos'] ?? '' }}
                            @if(!$loop->last) , @endif
                        @endforeach
                    @endif
                </td>
                <td style="border: 1px solid #cbd5e1; text-align: center;">{{ $item['tipo_patrullaje'] ?? 'N/A' }}</td>
                <td style="border: 1px solid #cbd5e1;">{{ $item['ubicacion'] ?? 'N/A' }}</td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colspan="6" style="border: 1px solid #cbd5e1; text-align: center; color: #94a3b8;">No hay personal de campo registrado.</td>
            </tr>
        @endif
        <tr><td colspan="6"></td></tr> <!-- Espacio -->

        <!-- Historial de Reportes -->
        @if(!empty($halconReportes))
            @php
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
                $colsCount = count($unidadesUnicas);
                $totalCols = max(6, $colsCount + 1);
            @endphp
            <tr>
                <td colspan="{{ $totalCols }}" style="background-color: #0f172a; color: #ffffff; font-size: 12pt; font-weight: bold; height: 30px;">
                    🕓 HISTORIAL DE REPORTES (OPTIMIZADO)
                </td>
            </tr>
            <tr style="background-color: #1e293b; color: #ffffff; font-weight: bold;">
                <td style="border: 1px solid #cbd5e1; text-align: center;">Hora</td>
                @foreach($unidadesUnicas as $unit)
                <td style="border: 1px solid #cbd5e1; text-align: center;">{{ str_contains($unit, ' ') ? $unit : 'Unidad ' . $unit }}</td>
                @endforeach
                @if($colsCount + 1 < 6)
                    @for($i = 0; $i < 6 - ($colsCount + 1); $i++)
                        <td></td>
                    @endfor
                @endif
            </tr>
            @foreach($halconReportes as $rep)
            <tr>
                <td style="border: 1px solid #cbd5e1; font-weight: bold; text-align: center; background-color: #f8fafc;">{{ $rep['hora'] }}</td>
                @foreach($unidadesUnicas as $unit)
                <td style="border: 1px solid #cbd5e1; text-align: center;">
                    @php
                        $status = '-';
                        if(isset($rep['rawData']) && is_array($rep['rawData'])) {
                            $uData = collect($rep['rawData'])->firstWhere('unidad', $unit);
                            if($uData) {
                                $status = $uData['observacion'] ?: 'OK';
                                if(stripos($status, 'no responde') !== false || stripos($status, 'no contesto') !== false) {
                                    $status = '❌';
                                }
                            }
                        }
                    @endphp
                    {{ $status }}
                </td>
                @endforeach
                @if($colsCount + 1 < 6)
                    @for($i = 0; $i < 6 - ($colsCount + 1); $i++)
                        <td></td>
                    @endfor
                @endif
            </tr>
            @endforeach
            <tr><td colspan="{{ $totalCols }}"></td></tr> <!-- Espacio -->
        @endif

        <!-- Visualizaciones Resaltantes (IA) -->
        <tr>
            <td colspan="6" style="background-color: #0f172a; color: #ffffff; font-size: 12pt; font-weight: bold; height: 30px;">
                ⚠️ VISUALIZACIONES RESALTANTES (IA)
            </td>
        </tr>
        <tr style="background-color: #e2e8f0; font-weight: bold;">
            <td colspan="2" style="border: 1px solid #cbd5e1;">Cámara</td>
            <td style="border: 1px solid #cbd5e1; text-align: center;">Hora</td>
            <td colspan="3" style="border: 1px solid #cbd5e1;">Descripción Corregida / Evento</td>
        </tr>
        @if(!empty($visualizacionesIA))
            @foreach($visualizacionesIA as $vis)
            <tr>
                <td colspan="2" style="border: 1px solid #cbd5e1; font-weight: bold; color: #1e3a8a;">{{ $vis['camara'] ?? 'N/A' }}</td>
                <td style="border: 1px solid #cbd5e1; text-align: center;">{{ $vis['hora'] ?? 'N/A' }}</td>
                <td colspan="3" style="border: 1px solid #cbd5e1; font-style: italic; color: #475569;">{{ $vis['corregido'] ?: ($vis['original'] ?? 'N/A') }}</td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colspan="6" style="border: 1px solid #cbd5e1; text-align: center; color: #94a3b8;">No hay visualizaciones resaltantes registradas.</td>
            </tr>
        @endif
        <tr><td colspan="6"></td></tr> <!-- Espacio -->

        <!-- Kilometraje de Unidades -->
        <tr>
            <td colspan="6" style="background-color: #0f172a; color: #ffffff; font-size: 12pt; font-weight: bold; height: 30px;">
                🚗 RECORRIDO Y KILOMETRAJE DE UNIDADES
            </td>
        </tr>
        <tr style="background-color: #e2e8f0; font-weight: bold;">
            <td style="border: 1px solid #cbd5e1; text-align: center;">Unidad</td>
            <td style="border: 1px solid #cbd5e1; text-align: center;">Placa</td>
            <td style="border: 1px solid #cbd5e1; text-align: center;">KM Final</td>
            <td style="border: 1px solid #cbd5e1; text-align: center;">Recorrido A/P</td>
            <td style="border: 1px solid #cbd5e1; text-align: center;">Total P/O</td>
            <td style="border: 1px solid #cbd5e1; text-align: center;">Códigos</td>
        </tr>
        @if($reporte->asignaciones->isNotEmpty())
            @foreach($reporte->asignaciones as $asig)
            <tr>
                <td style="border: 1px solid #cbd5e1; font-weight: bold; text-align: center; color: #1e3a8a;">{{ $asig->unidad_id }}</td>
                <td style="border: 1px solid #cbd5e1; text-align: center;">{{ $asig->placa }}</td>
                <td style="border: 1px solid #cbd5e1; text-align: center; font-weight: bold;">{{ $asig->km }}</td>
                <td style="border: 1px solid #cbd5e1; text-align: center;">{{ $asig->ap }}</td>
                <td style="border: 1px solid #cbd5e1; text-align: center;">{{ $asig->po }}</td>
                <td style="border: 1px solid #cbd5e1; text-align: center; font-weight: bold; color: #3b82f6;">{{ $asig->cod_po ?: '-' }}</td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colspan="6" style="border: 1px solid #cbd5e1; text-align: center; color: #94a3b8;">No hay registros de kilometraje.</td>
            </tr>
        @endif
    </table>
</body>
</html>
