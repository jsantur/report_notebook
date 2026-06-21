<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ResumenSheet implements FromArray, WithTitle, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $reporte;

    public function __construct($reporte)
    {
        $this->reporte = $reporte;
    }

    public function array(): array
    {
        $sups = $this->reporte->supervisores_camaras_list;
        $supervisorCamaras = $sups->map(fn($s) => $s->nombres . ' ' . $s->apellido_paterno)->join(' / ') ?: 'N/A';

        return [
            ['Campo', 'Valor'],
            ['Tipo de Reporte', 'Turno - ' . ($this->reporte->turno ?? 'N/A')],
            ['Responsable', strtoupper($this->reporte->creator->name ?? 'SANTUR MOGOLLÓN JOSEPH')], // Using current user name if creator not tracked
            ['Fecha', $this->reporte->fecha],
            ['Hora Registro', $this->reporte->hora],
            ['Supervisor de Campo', ($this->reporte->supervisorCampo->nombres ?? '') . ' ' . ($this->reporte->supervisorCampo->apellido_paterno ?? '')],
            ['Supervisor de Cámaras', $supervisorCamaras],
        ];
    }

    public function headings(): array
    {
        return []; // Headings are in the array for this specific sheet
    }

    public function title(): string
    {
        return 'Resumen';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:B1')->getFont()->setBold(true);
        $sheet->getStyle('A1:B1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF0056B3'); // Blue
        $sheet->getStyle('A1:B1')->getFont()->getColor()->setARGB('FFFFFFFF'); // White text
        
        $sheet->getStyle('A1:B7')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    }
}
