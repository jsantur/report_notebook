<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LogisticaSheet implements FromArray, WithTitle, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $reporte;

    public function __construct($reporte)
    {
        $this->reporte = $reporte;
    }

    public function array(): array
    {
        $text = $this->reporte->ocurrencias_relevo;
        $data = [['Item', 'Cantidad']];
        
        $items = [
            'Monitores' => '/(\d+)\s*Monitores/i',
            'Radio Tetra' => '/(\d+)\s*Radio\s*Tetra/i',
            'Baterías' => '/(\d+)\s*Bater[íi]as/i',
            'Cargador' => '/(\d+)\s*Cargador/i',
            'Radios Motorola' => '/(\d+)\s*Radios\s*Motorola/i',
            'Extintor' => '/(\d+)\s*Extintor/i',
            'Ventiladores' => '/(\d+)\s*Ventiladores/i',
            'Stand' => '/(\d+)\s*Stand/i',
        ];

        foreach ($items as $label => $pattern) {
            $qty = 'N/A';
            if (preg_match($pattern, $text, $matches)) {
                $qty = $matches[1];
            }
            $data[] = [$label, $qty];
        }

        return $data;
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Logística';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:B1')->getFont()->setBold(true);
        $sheet->getStyle('A1:B1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF0056B3');
        $sheet->getStyle('A1:B1')->getFont()->getColor()->setARGB('FFFFFFFF');
        
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:B'.$lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    }
}
