<?php

namespace App\Exports;

use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Supply;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class StockReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths, WithEvents
{
    protected int        $month;
    protected int        $year;
    protected array      $warehouseIds;
    protected string     $warehouseName;
    protected Collection $rows;

    public function __construct(int $month, int $year, array $warehouseIds, string $warehouseName = 'Tất cả kho')
    {
        $this->month         = $month;
        $this->year          = $year;
        $this->warehouseIds  = $warehouseIds;
        $this->warehouseName = $warehouseName;

        $this->rows = $this->buildRows();
    }

    // ─────────────────────────────────────────────────────────
    //  BUILD DATA
    // ─────────────────────────────────────────────────────────

    private function buildRows(): Collection
    {
        // Mốc thời gian kỳ báo cáo
        $periodStart = \Carbon\Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
        $periodEnd   = $periodStart->copy()->endOfMonth();

        // Lấy tất cả supplies có giao dịch với kho này
        $supplyIds = collect()
            ->merge(
                StockIn::whereIn('warehouse_id', $this->warehouseIds)->pluck('supply_id')
            )
            ->merge(
                StockOut::whereIn('warehouse_id', $this->warehouseIds)->pluck('supply_id')
            )
            ->unique()
            ->values();

        $supplies = Supply::whereIn('id', $supplyIds)->orderBy('name')->get();

        // Tồn đầu kỳ = tổng nhập trước tháng - tổng xuất trước tháng
        $openingIns = StockIn::whereIn('warehouse_id', $this->warehouseIds)
            ->whereIn('supply_id', $supplyIds)
            ->where('received_date', '<', $periodStart)
            ->selectRaw('supply_id, SUM(quantity) as total')
            ->groupBy('supply_id')
            ->pluck('total', 'supply_id');

        $openingOuts = StockOut::whereIn('warehouse_id', $this->warehouseIds)
            ->whereIn('supply_id', $supplyIds)
            ->where('exported_date', '<', $periodStart)
            ->selectRaw('supply_id, SUM(quantity) as total')
            ->groupBy('supply_id')
            ->pluck('total', 'supply_id');

        // Nhập trong kỳ
        $periodIns = StockIn::whereIn('warehouse_id', $this->warehouseIds)
            ->whereIn('supply_id', $supplyIds)
            ->whereBetween('received_date', [$periodStart, $periodEnd])
            ->selectRaw('supply_id, SUM(quantity) as total')
            ->groupBy('supply_id')
            ->pluck('total', 'supply_id');

        // Xuất trong kỳ
        $periodOuts = StockOut::whereIn('warehouse_id', $this->warehouseIds)
            ->whereIn('supply_id', $supplyIds)
            ->whereBetween('exported_date', [$periodStart, $periodEnd])
            ->selectRaw('supply_id, SUM(quantity) as total')
            ->groupBy('supply_id')
            ->pluck('total', 'supply_id');

        return $supplies->map(function ($supply, $idx) use ($openingIns, $openingOuts, $periodIns, $periodOuts) {
            $opening = ($openingIns[$supply->id] ?? 0) - ($openingOuts[$supply->id] ?? 0);
            $opening = max(0, $opening); // không âm
            $in      = $periodIns[$supply->id]  ?? 0;
            $out     = $periodOuts[$supply->id] ?? 0;
            $closing = max(0, $opening + $in - $out);

            return [
                'stt'     => $idx + 1,
                'name'    => $supply->name,
                'unit'    => $supply->unit ?? '—',
                'opening' => $opening,
                'in'      => $in,
                'out'     => $out,
                'closing' => $closing,
            ];
        });
    }

    public function collection(): Collection
    {
        return $this->rows->map(fn($r) => [
            $r['stt'],
            $r['name'],
            $r['unit'],
            $r['opening'],
            $r['in'],
            $r['out'],
            $r['closing'],
        ]);
    }

    public function headings(): array
    {
        return [
            'STT',
            'Mặt hàng',
            'Đơn vị',
            'Tồn đầu kỳ',
            'Nhập trong kỳ',
            'Xuất trong kỳ',
            'Tồn cuối kỳ',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 30,
            'C' => 12,
            'D' => 16,
            'E' => 16,
            'F' => 16,
            'G' => 16,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Header row (row 3 vì có 2 dòng tiêu đề phía trên)
        return [
            3 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D9488']],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                    'wrapText'   => true,
                ],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCBD5E1']]],
            ],
        ];
    }

    public function title(): string
    {
        return 'Báo cáo tháng ' . $this->month . '-' . $this->year;
    }

    public function registerEvents(): array
    {
        $month         = $this->month;
        $year          = $this->year;
        $warehouseName = $this->warehouseName;
        $rowCount      = $this->rows->count();

        return [
            AfterSheet::class => function (AfterSheet $event) use ($month, $year, $warehouseName, $rowCount) {
                $sheet = $event->sheet->getDelegate();

                // ── Chèn 2 dòng tiêu đề ở đầu ──────────────────────────────
                $sheet->insertNewRowBefore(1, 2);

                // Dòng 1: Tên báo cáo
                $sheet->mergeCells('A1:G1');
                $sheet->setCellValue('A1', 'BÁO CÁO NHẬP XUẤT KHO – THÁNG ' . $month . '/' . $year);
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF0F172A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE0F2FE']],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(28);

                // Dòng 2: Kho + Ngày xuất
                $sheet->mergeCells('A2:G2');
                $sheet->setCellValue('A2', 'Kho: ' . $warehouseName . '   |   Ngày xuất báo cáo: ' . now()->format('d/m/Y H:i'));
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['size' => 9, 'italic' => true, 'color' => ['argb' => 'FF64748B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF8FAFC']],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(18);

                // ── Style cho body rows ──────────────────────────────────────
                $lastRow = $rowCount + 3; // 2 header + 1 heading + rows
                if ($rowCount > 0) {
                    // Căn phải cột số
                    foreach (['D', 'E', 'F', 'G'] as $col) {
                        $sheet->getStyle("{$col}4:{$col}{$lastRow}")
                            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        $sheet->getStyle("{$col}4:{$col}{$lastRow}")
                            ->getNumberFormat()->setFormatCode('#,##0');
                    }

                    // Borders toàn bảng
                    $sheet->getStyle("A3:G{$lastRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']],
                        ],
                    ]);

                    // Zebra stripes cho data rows
                    for ($row = 4; $row <= $lastRow; $row += 2) {
                        $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF8FAFC']],
                        ]);
                    }

                    // Dòng tổng cộng
                    $totalRow = $lastRow + 1;
                    $sheet->mergeCells("A{$totalRow}:C{$totalRow}");
                    $sheet->setCellValue("A{$totalRow}", 'TỔNG CỘNG');
                    $sheet->setCellValue("D{$totalRow}", "=SUM(D4:D{$lastRow})");
                    $sheet->setCellValue("E{$totalRow}", "=SUM(E4:E{$lastRow})");
                    $sheet->setCellValue("F{$totalRow}", "=SUM(F4:F{$lastRow})");
                    $sheet->setCellValue("G{$totalRow}", "=SUM(G4:G{$lastRow})");
                    $sheet->getStyle("A{$totalRow}:G{$totalRow}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['argb' => 'FF0F172A']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD1FAE5']],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF0D9488']]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ]);
                    $sheet->getStyle("A{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    foreach (['D', 'E', 'F', 'G'] as $col) {
                        $sheet->getStyle("{$col}{$totalRow}")
                            ->getNumberFormat()->setFormatCode('#,##0');
                    }
                }

                // Căn giữa cột STT và đơn vị
                $sheet->getStyle("A3:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C3:C{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Freeze panes tại A4 (giữ header cố định khi scroll)
                $sheet->freezePane('A4');
            },
        ];
    }
}
