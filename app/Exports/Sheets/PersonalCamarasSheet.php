<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PersonalCamarasSheet implements FromArray, WithTitle, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $reporte;

    public function __construct($reporte)
    {
        $this->reporte = $reporte;
    }

    public function array(): array
    {
        $data = is_string($this->reporte->distribucion_personal_camaras) ? json_decode($this->reporte->distribucion_personal_camaras, true) : ($this->reporte->distribucion_personal_camaras ?? []);
        $rows = [['Nombre', 'Máquina', 'Cámaras Asignadas']];

        foreach ($data as $op) {
            $camaras = !empty($op['camaras']) 
                ? (is_array($op['camaras']) ? implode(', ', $op['camaras']) : $op['camaras'])
                : 'Sin cámaras asignadas';
            
            $rows[] = [
                ($op['nombres'] ?? '') . ' ' . ($op['apellido_paterno'] ?? ''),
                $op['maquina'] ?? 'N/A',
                $camaras
            ];
        }

        return $rows;
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Personal Cámaras';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $sheet->getStyle('A1:C1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF0056B3');
        $sheet->getStyle('A1:C1')->getFont()->getColor()->setARGB('FFFFFFFF');
        
        $lastRow = $sheet->getHighestRow();
        if ($lastRow > 1) {
            $sheet->getStyle('A1:C'.$lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        }
    }
}
