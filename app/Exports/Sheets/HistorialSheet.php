<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HistorialSheet implements FromArray, WithTitle, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $reporte;

    public function __construct($reporte)
    {
        $this->reporte = $reporte;
    }

    public function array(): array
    {
        $data = is_string($this->reporte->reporte_personal_patrullando) ? json_decode($this->reporte->reporte_personal_patrullando, true) : ($this->reporte->reporte_personal_patrullando ?? []);
        if (empty($data)) return [['Sin historial registrado']];

        // Extract all unique units to create columns
        $units = [];
        foreach ($data as $entry) {
            foreach ($entry['unidades'] ?? [] as $u => $val) {
                if (!in_array($u, $units)) $units[] = $u;
            }
        }
        sort($units);

        $header = array_merge(['Hora'], $units);
        $rows = [$header];

        foreach ($data as $entry) {
            $row = [$entry['hora'] ?? 'N/A'];
            foreach ($units as $u) {
                $row[] = $entry['unidades'][$u] ?? '-';
            }
            $rows[] = $row;
        }

        return $rows;
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Historial';
    }

    public function styles(Worksheet $sheet)
    {
        $lastCol = $sheet->getHighestColumn();
        $sheet->getStyle('A1:'.$lastCol.'1')->getFont()->setBold(true);
        $sheet->getStyle('A1:'.$lastCol.'1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF0056B3');
        $sheet->getStyle('A1:'.$lastCol.'1')->getFont()->getColor()->setARGB('FFFFFFFF');
        
        $lastRow = $sheet->getHighestRow();
        if ($lastRow > 1) {
            $sheet->getStyle('A1:'.$lastCol.$lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        }
    }
}
