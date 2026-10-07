<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataExport implements FromCollection, WithCustomStartCell, WithEvents, WithHeadings, WithStyles, WithTitle
{
    protected Collection $data;

    protected array $headings;

    protected string $title;

    protected string $sheetTitle;

    protected array $hyperlinkColumns;

    protected array $totals;

    protected string $footnote;

    protected array $highlightCells;

    public function __construct(Collection $data, array $headings, string $title, string $sheetTitle = 'Data', array $hyperlinkColumns = [], array $totals = [], string $footnote = '', array $highlightCells = [])
    {
        $this->data = $data;
        $this->headings = $headings;
        $this->title = $title;
        $this->sheetTitle = $sheetTitle;
        $this->hyperlinkColumns = $hyperlinkColumns;
        $this->totals = $totals;
        $this->footnote = $footnote;
        $this->highlightCells = $highlightCells;
    }

    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }

    public function startCell(): string
    {
        return 'A3';
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A3:'.$sheet->getHighestColumn().'3')->getFont()->setBold(true);
        $sheet->getStyle('A3:'.$sheet->getHighestColumn().'3')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('6C5CFF');
        $sheet->getStyle('A3:'.$sheet->getHighestColumn().'3')->getFont()->getColor()->setRGB('FFFFFF');

        $lastRow = $sheet->getHighestRow();
        $lastCol = $sheet->getHighestColumn();
        $sheet->getStyle("A4:{$lastCol}{$lastRow}")->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->mergeCells('A1:'.$sheet->getHighestColumn().'1');
                $sheet->setCellValue('A1', 'Johen Office Management System');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('A2:'.$sheet->getHighestColumn().'2');
                $sheet->setCellValue('A2', $this->title);
                $sheet->getStyle('A2')->getFont()->setSize(11)->setItalic(true);
                $sheet->getStyle('A2')->getFont()->getColor()->setRGB('666666');
                $sheet->getStyle('A2')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                foreach (range('A', $sheet->getHighestColumn()) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                if (! empty($this->hyperlinkColumns)) {
                    $headings = $this->headings();
                    foreach ($this->hyperlinkColumns as $colName) {
                        $colIndex = array_search($colName, $headings);
                        if ($colIndex === false) {
                            continue;
                        }
                        $colLetter = Coordinate::stringFromColumnIndex($colIndex + 1);
                        for ($row = 4; $row <= $sheet->getHighestRow(); $row++) {
                            $cell = "{$colLetter}{$row}";
                            $url = $sheet->getCell($cell)->getValue();
                            if ($url && filter_var($url, FILTER_VALIDATE_URL)) {
                                $sheet->getCell($cell)->getHyperlink()->setUrl($url);
                                $sheet->getStyle($cell)->getFont()->getColor()->setRGB('6C5CFF');
                                $sheet->getStyle($cell)->getFont()->setUnderline(true);
                            }
                        }
                    }
                }

                if (! empty($this->highlightCells)) {
                    $headings = $this->headings();
                    foreach ($this->highlightCells as $cell) {
                        $colIndex = array_search($cell['column'], $headings);
                        if ($colIndex === false) {
                            continue;
                        }
                        $colLetter = Coordinate::stringFromColumnIndex($colIndex + 1);
                        $range = "{$colLetter}4:{$colLetter}{$sheet->getHighestRow()}";

                        $conditional = new Conditional;
                        $conditional->setConditionType(Conditional::CONDITION_CELLIS);
                        $conditional->setOperatorType(Conditional::OPERATOR_EQUAL);
                        $conditional->addCondition('"'.$cell['value'].'"');
                        $conditional->getStyle()->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FF'.($cell['fill'] ?? 'D1FAE5'));

                        $existing = $sheet->getConditionalStyles($range);
                        $existing[] = $conditional;
                        $sheet->setConditionalStyles($range, $existing);
                    }
                }

                if (! empty($this->totals)) {
                    $lastDataRow = $sheet->getHighestRow();
                    $totalRow = $lastDataRow + 2;
                    $firstDataRow = 4;

                    $sheet->setCellValue("A{$totalRow}", 'TOTAL');
                    $sheet->getStyle("A{$totalRow}")->getFont()->setBold(true)->setSize(11);
                    $sheet->getStyle("A{$totalRow}")->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('6C5CFF');
                    $sheet->getStyle("A{$totalRow}")->getFont()->getColor()->setRGB('FFFFFF');

                    foreach ($this->totals as $total) {
                        $colLetter = $total['column'];

                        for ($row = $firstDataRow; $row <= $lastDataRow; $row++) {
                            $dataCell = "{$colLetter}{$row}";
                            $val = $sheet->getCell($dataCell)->getValue();
                            if (is_numeric($val)) {
                                $sheet->getStyle($dataCell)->getNumberFormat()->setFormatCode('#,##0');
                            }
                        }

                        $totalCell = "{$colLetter}{$totalRow}";
                        $formula = "=SUM({$colLetter}{$firstDataRow}:{$colLetter}{$lastDataRow})";
                        $sheet->setCellValue($totalCell, $formula);
                        $sheet->getStyle($totalCell)->getNumberFormat()->setFormatCode('#,##0');
                        $sheet->getStyle($totalCell)->getFont()->setBold(true)->setSize(11);
                        $sheet->getStyle($totalCell)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('6C5CFF');
                        $sheet->getStyle($totalCell)->getFont()->getColor()->setRGB('FFFFFF');
                    }

                    $highestCol = $sheet->getHighestColumn();
                    $sheet->getStyle("A{$totalRow}:{$highestCol}{$totalRow}")->getBorders()->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN);
                }

                if ($this->footnote !== '') {
                    $noteRow = $sheet->getHighestRow() + 2;
                    $highestCol = $sheet->getHighestColumn();
                    $sheet->mergeCells("A{$noteRow}:{$highestCol}{$noteRow}");
                    $sheet->setCellValue("A{$noteRow}", $this->footnote);
                    $sheet->getStyle("A{$noteRow}")->getFont()->setItalic(true)->setSize(10);
                    $sheet->getStyle("A{$noteRow}")->getFont()->getColor()->setRGB('34D399');
                }
            },
        ];
    }
}
