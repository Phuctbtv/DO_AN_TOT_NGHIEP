<?php

namespace App\Exports;

use App\Models\Trip;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TripsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected string|null $status;
    protected string|null $dateFrom;
    protected string|null $dateTo;

    private int $rowIndex = 0;

    public function __construct(
        ?string $status   = null,
        ?string $dateFrom = null,
        ?string $dateTo   = null,
    ) {
        $this->status   = $status;
        $this->dateFrom = $dateFrom;
        $this->dateTo   = $dateTo;
    }

    public function query()
    {
        return Trip::with(['driver', 'warehouse', 'deliveries'])
            ->when($this->status,   fn($q) => $q->where('status', $this->status))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'STT',
            'Mã chuyến',
            'Tài xế',
            'Kho xuất',
            'Phương tiện',
            'Số điểm giao',
            'Đã giao',
            'Trạng thái',
            'Ngày tạo',
            'Ngày xuất kho',
            'Ngày hoàn thành',
        ];
    }

    public function map($row): array
    {
        $this->rowIndex++;

        $totalDeliveries = $row->deliveries->count();
        $doneDeliveries  = $row->deliveries->whereIn('status', ['success', 'warning'])->count();

        $statusLabel = match ($row->status) {
            'preparing' => 'Chuẩn bị',
            'exporting' => 'Xuất kho',
            'shipping'  => 'Đang giao',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã huỷ',
            default     => $row->status,
        };

        return [
            $this->rowIndex,
            $row->trip_code,
            $row->driver?->name ?? '—',
            $row->warehouse?->name ?? '—',
            $row->vehicle_info,
            $totalDeliveries,
            $doneDeliveries,
            $statusLabel,
            $row->created_at?->format('d/m/Y H:i') ?? '—',
            $row->exported_at?->format('d/m/Y H:i') ?? '—',
            $row->completed_at?->format('d/m/Y H:i') ?? '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0891B2']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCBD5E1']]],
            ],
        ];
    }

    public function title(): string
    {
        return 'Danh sách Chuyến xe';
    }
}
