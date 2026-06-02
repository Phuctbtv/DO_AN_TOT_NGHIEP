<?php

namespace App\Http\Controllers;

use App\Exports\HouseholdsExport;
use App\Exports\TripsExport;
use App\Models\Trip;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    // ============================================================
    //  1. XUẤT EXCEL – Hộ dân
    // ============================================================

    public function exportHouseholds(Request $request)
    {
        $request->validate([
            'status'    => ['nullable', 'in:pending,active,rejected'],
            'priority'  => ['nullable', 'integer', 'between:1,3'],
            'date_from' => ['nullable', 'date'],
            'date_to'   => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $status   = $request->input('status');
        $priority = $request->input('priority') ? (int) $request->input('priority') : null;
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');

        // Tên file theo bộ lọc
        $statusSuffix = $status ? "_$status" : '_all';
        $dateSuffix   = ($dateFrom && $dateTo) ? "_{$dateFrom}_{$dateTo}" : '';
        $fileName     = 'BaoCao_HoDan' . $statusSuffix . $dateSuffix . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new HouseholdsExport($status, $priority, $dateFrom, $dateTo),
            $fileName
        );
    }

    // ============================================================
    //  2. XUẤT EXCEL – Chuyến xe
    // ============================================================

    public function exportTrips(Request $request)
    {
        $request->validate([
            'status'    => ['nullable', 'in:preparing,exporting,shipping,completed,cancelled'],
            'date_from' => ['nullable', 'date'],
            'date_to'   => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $status   = $request->input('status');
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');

        $statusSuffix = $status ? "_$status" : '_all';
        $dateSuffix   = ($dateFrom && $dateTo) ? "_{$dateFrom}_{$dateTo}" : '';
        $fileName     = 'BaoCao_ChuyenXe' . $statusSuffix . $dateSuffix . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new TripsExport($status, $dateFrom, $dateTo),
            $fileName
        );
    }

    // ============================================================
    //  3. XUẤT PDF – Chi tiết chuyến xe
    // ============================================================

    public function tripPdf(Trip $trip)
    {
        $trip->load([
            'driver',
            'warehouse',
            'creator',
            'tripDetails.supply.category',
            'deliveries.household.resident',
        ]);

        $pdf = Pdf::loadView('reports.trip_pdf', compact('trip'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isRemoteEnabled' => false,
                'isFontSubsettingEnabled' => true,
            ]);

        $fileName = 'ChiTiet_' . $trip->trip_code . '_' . now()->format('Ymd') . '.pdf';

        return $pdf->stream($fileName);
    }
}
