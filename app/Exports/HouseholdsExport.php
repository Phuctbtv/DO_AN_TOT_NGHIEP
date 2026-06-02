<?php

namespace App\Exports;

use App\Models\Household;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class HouseholdsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected string|null $status;
    protected int|null    $priority;
    protected string|null $dateFrom;
    protected string|null $dateTo;

    private int $rowIndex = 0;

    public function __construct(
        ?string $status   = null,
        ?int    $priority = null,
        ?string $dateFrom = null,
        ?string $dateTo   = null,
    ) {
        $this->status   = $status;
        $this->priority = $priority;
        $this->dateFrom = $dateFrom;
        $this->dateTo   = $dateTo;
    }

    public function query()
    {
        return Household::with('resident')
            ->when($this->status,   fn($q) => $q->where('status', $this->status))
            ->when($this->priority, fn($q) => $q->where('priority_level', $this->priority))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'STT',
            'Họ và tên',
            'CCCD / CMND',
            'Số điện thoại',
            'Địa chỉ',
            'Số thành viên',
            'Mức ưu tiên',
            'Trạng thái',
            'Ngày đăng ký',
            'Ngày duyệt',
        ];
    }

    public function map($row): array
    {
        $this->rowIndex++;

        $priorityLabel = match ($row->priority_level) {
            1 => 'Cấp 1 - Khẩn cấp',
            2 => 'Cấp 2 - Cần thiết',
            3 => 'Cấp 3 - Bình thường',
            default => '—',
        };

        $statusLabel = match ($row->status) {
            'pending'  => 'Chờ duyệt',
            'active'   => 'Đã duyệt',
            'rejected' => 'Từ chối',
            default    => $row->status,
        };

        // Ngày duyệt: chỉ có khi status = active
        $approvedAt = $row->status === 'active' && $row->updated_at
            ? $row->updated_at->format('d/m/Y H:i')
            : '—';

        return [
            $this->rowIndex,
            $row->household_name,
            $row->resident?->identity_card ?? '—',
            $row->phone ?? '—',
            $row->address,
            $row->member_count ?? 1,
            $priorityLabel,
            $statusLabel,
            $row->created_at?->format('d/m/Y H:i') ?? '—',
            $approvedAt,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Header row style
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D9488']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCBD5E1']]],
            ],
        ];
    }

    public function title(): string
    {
        return 'Danh sách Hộ dân';
    }
}
