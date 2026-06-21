<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EstadoOperativoSheet implements FromArray, WithTitle, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $reporte;

    public function __construct($reporte)
    {
        $this->reporte = $reporte;
    }

    public function array(): array
    {
        $text = $this->reporte->ocurrencias_relevo;
        $camaras = 'N/A';
        $postes = 'N/A';

        // Basic parsing attempt
        if (preg_match('/(\d+)\s*C[AÁ]MARAS\s*OPERATIVAS/i', $text, $matches)) {
            $camaras = $matches[1];
        }
        if (preg_match('/(\d+)\s*POSTES\s*DE\s*EMERGENCIA/i', $text, $matches)) {
            $postes = $matches[1];
        }

        return [
            ['Elemento', 'Cantidad', 'Estado'],
            ['Cámaras', $camaras, $camaras !== 'N/A' ? 'Operativas' : 'Información en texto'],
            ['Postes de Emergencia', $postes, $postes !== 'N/A' ? 'Activos' : 'Información en texto'],
        ];
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Estado Operativo';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $sheet->getStyle('A1:C1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF0056B3');
        $sheet->getStyle('A1:C1')->getFont()->getColor()->setARGB('FFFFFFFF');
        
        $sheet->getStyle('A1:C3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    }
}
