<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PersonalCampoSheet implements FromArray, WithTitle, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $reporte;

    public function __construct($reporte)
    {
        $this->reporte = $reporte;
    }

    public function array(): array
    {
        $data = is_string($this->reporte->distribucion_personal_campo) ? json_decode($this->reporte->distribucion_personal_campo, true) : ($this->reporte->distribucion_personal_campo ?? []);
        $rows = [['Unidad', 'Placa', 'Personal', 'Tipo', 'Ubicación', 'Códigos']];

        foreach ($data as $item) {
            $personal = '';
            if (($item['tipo_patrullaje'] ?? '') === 'Vehicular') {
                $personal = 'Chofer: ' . ($item['chofer'] ?? '') . ' / Op: ' . ($item['operador'] ?? '');
                if (!empty($item['lince'])) $personal .= ' / Lince: ' . $item['lince'];
            } elseif (($item['tipo_patrullaje'] ?? '') === 'Motorizado') {
                $personal = 'Chofer: ' . ($item['chofer'] ?? '');
            } else {
                $personal = 'Sereno: ' . ($item['sereno'] ?? '');
            }

            if (!empty($item['patrullaje_integrado'])) {
                $integrado = [];
                foreach ($item['patrullaje_integrado'] as $pnp) {
                    $integrado[] = ($pnp['grado'] ?? '') . ' ' . ($pnp['nombre'] ?? '') . ' ' . ($pnp['apellidos'] ?? '');
                }
                $personal .= "\nIntegrado: " . implode(', ', $integrado);
            }

            $rows[] = [
                $item['unidad'] ?? 'N/A',
                $item['matricula'] ?? 'N/A',
                $personal,
                $item['tipo_patrullaje'] ?? 'N/A',
                $item['sector'] ?? 'N/A',
                $item['cod_po'] ?? '-'
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
        return 'Campo';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF0056B3');
        $sheet->getStyle('A1:F1')->getFont()->getColor()->setARGB('FFFFFFFF');
        
        $lastRow = $sheet->getHighestRow();
        if ($lastRow > 1) {
            $sheet->getStyle('A1:F'.$lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('C2:C'.$lastRow)->getAlignment()->setWrapText(true);
        }
    }
}
