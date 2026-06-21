<?php

namespace App\Exports;

use App\Models\Reporte;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ReporteExport implements FromView, ShouldAutoSize, WithTitle, WithDrawings
{
    protected $reporte;

    public function __construct(Reporte $reporte)
    {
        $this->reporte = $reporte->load(['supervisorCampo', 'supervisorCamaras', 'asignaciones']);
    }

    public function view(): View
    {
        $reporte = $this->reporte;
        $distribucionCamaras = is_string($reporte->distribucion_personal_camaras) ? json_decode($reporte->distribucion_personal_camaras, true) : ($reporte->distribucion_personal_camaras ?? []);
        $distribucionCampo = is_string($reporte->distribucion_personal_campo) ? json_decode($reporte->distribucion_personal_campo, true) : ($reporte->distribucion_personal_campo ?? []);
        $halconReportes = is_string($reporte->reporte_personal_patrullando) ? json_decode($reporte->reporte_personal_patrullando, true) : ($reporte->reporte_personal_patrullando ?? []);
        $visualizacionesIA = is_string($reporte->visualizaciones_resaltantes) ? json_decode($reporte->visualizaciones_resaltantes, true) : ($reporte->visualizaciones_resaltantes ?? []);

        return view('reportes.excel', compact(
            'reporte',
            'distribucionCamaras',
            'distribucionCampo',
            'halconReportes',
            'visualizacionesIA'
        ));
    }

    public function title(): string
    {
        return 'Reporte Turno ' . $this->reporte->turno;
    }

    public function drawings()
    {
        $logoPath = public_path('img/logo_reportePDF.png');
        
        // PhpSpreadsheet's Drawing class requires mime_content_type to be available.
        if (file_exists($logoPath) && function_exists('mime_content_type')) {
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo institucional');
            $drawing->setPath($logoPath);
            $drawing->setHeight(70);
            $drawing->setCoordinates('C1');
            $drawing->setOffsetX(35); // Centra el logo sobre las columnas C y D
            $drawing->setOffsetY(10);
            return $drawing;
        }
        
        return [];
    }
}
