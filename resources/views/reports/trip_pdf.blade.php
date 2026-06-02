<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Chi Tiết Chuyến Xe - {{ $trip->trip_code }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: DejaVu Sans, Arial, sans-serif;
    font-size: 10pt;
    color: #1e293b;
    background: #fff;
  }

  /* ── HEADER ──────────────────────────────────── */
  .header {
    border-bottom: 3px solid #0d9488;
    padding-bottom: 12px;
    margin-bottom: 16px;
    display: table;
    width: 100%;
  }
  .header-left {
    display: table-cell;
    vertical-align: middle;
    width: 60%;
  }
  .header-right {
    display: table-cell;
    vertical-align: middle;
    text-align: right;
    width: 40%;
  }
  .brand-name {
    font-size: 20pt;
    font-weight: bold;
    color: #0d9488;
    letter-spacing: 2px;
  }
  .brand-sub {
    font-size: 8pt;
    color: #64748b;
    margin-top: 2px;
  }
  .doc-title {
    font-size: 14pt;
    font-weight: bold;
    color: #0f172a;
    text-transform: uppercase;
    letter-spacing: 1px;
  }
  .doc-code {
    font-size: 12pt;
    font-weight: bold;
    color: #0d9488;
    font-family: Courier New, monospace;
    margin-top: 3px;
  }
  .doc-date {
    font-size: 8pt;
    color: #64748b;
    margin-top: 3px;
  }

  /* ── STATUS BADGE ─────────────────────────────── */
  .status-badge {
    display: inline-block;
    padding: 3px 12px;
    border-radius: 20px;
    font-size: 9pt;
    font-weight: bold;
  }

  /* ── SECTION TITLE ────────────────────────────── */
  .section-title {
    font-size: 10pt;
    font-weight: bold;
    color: #0d9488;
    border-left: 3px solid #0d9488;
    padding-left: 8px;
    margin-bottom: 8px;
    margin-top: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  /* ── INFO GRID ────────────────────────────────── */
  .info-grid {
    display: table;
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 4px;
  }
  .info-row {
    display: table-row;
  }
  .info-label {
    display: table-cell;
    width: 30%;
    padding: 4px 8px 4px 0;
    font-size: 8.5pt;
    color: #64748b;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    vertical-align: top;
  }
  .info-value {
    display: table-cell;
    width: 70%;
    padding: 4px 0;
    font-size: 9.5pt;
    font-weight: 600;
    color: #0f172a;
    vertical-align: top;
  }

  /* ── TIMELINE BOXES ───────────────────────────── */
  .timeline-row {
    display: table;
    width: 100%;
    border-collapse: separate;
    border-spacing: 6px;
    margin-bottom: 6px;
  }
  .timeline-cell {
    display: table-cell;
    width: 25%;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 6px 8px;
    vertical-align: top;
  }
  .timeline-label {
    font-size: 7.5pt;
    color: #94a3b8;
    font-weight: bold;
    text-transform: uppercase;
    margin-bottom: 2px;
  }
  .timeline-value {
    font-size: 8.5pt;
    font-weight: bold;
    color: #0f172a;
  }

  /* ── TABLE CHUNG ──────────────────────────────── */
  table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 6px;
  }
  thead tr {
    background-color: #0d9488;
    color: #fff;
  }
  thead th {
    padding: 6px 8px;
    font-size: 8.5pt;
    font-weight: bold;
    text-align: left;
    border: 1px solid #0a7a70;
  }
  tbody tr:nth-child(even) {
    background-color: #f8fafc;
  }
  tbody td {
    padding: 5px 8px;
    font-size: 8.5pt;
    border: 1px solid #e2e8f0;
    vertical-align: middle;
    color: #334155;
  }
  .text-center { text-align: center; }
  .text-right  { text-align: right; }

  /* ── STATUS CELLS ─────────────────────────────── */
  .status-success { color: #059669; font-weight: bold; }
  .status-pending { color: #b45309; font-weight: bold; }
  .status-failed  { color: #dc2626; font-weight: bold; }
  .status-warning { color: #92400e; font-weight: bold; }

  /* ── DIVIDER ──────────────────────────────────── */
  .divider {
    border: none;
    border-top: 1px solid #e2e8f0;
    margin: 10px 0;
  }

  /* ── FOOTER ───────────────────────────────────── */
  .footer {
    margin-top: 20px;
    border-top: 2px solid #e2e8f0;
    padding-top: 12px;
  }
  .footer-row {
    display: table;
    width: 100%;
  }
  .footer-left {
    display: table-cell;
    width: 50%;
    vertical-align: top;
    font-size: 8pt;
    color: #64748b;
  }
  .footer-right {
    display: table-cell;
    width: 50%;
    text-align: center;
    vertical-align: top;
  }
  .sign-box {
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 8px 16px;
    display: inline-block;
    min-width: 200px;
  }
  .sign-title {
    font-size: 9pt;
    font-weight: bold;
    color: #0f172a;
    margin-bottom: 40px;
  }
  .sign-name {
    font-size: 8pt;
    color: #64748b;
  }
</style>
</head>
<body>

{{-- ═══════════════ HEADER ═══════════════ --}}
<div class="header">
  <div class="header-left">
    <div class="brand-name">ĐẠI PHÚC</div>
    <div class="brand-sub">Hệ thống Cứu trợ &amp; Phân phối Hàng hoá</div>
  </div>
  <div class="header-right">
    <div class="doc-title">Chi Tiết Chuyến Xe</div>
    <div class="doc-code">{{ $trip->trip_code }}</div>
    <div class="doc-date">Ngày xuất: {{ now()->format('H:i – d/m/Y') }}</div>
  </div>
</div>

{{-- ═══════════════ THÔNG TIN CHUYẾN XE ═══════════════ --}}
<div class="section-title">📋 Thông tin chuyến xe</div>

@php
  $statusConfig = [
    'preparing' => ['label' => 'Chuẩn bị',   'bg' => '#fef3c7', 'color' => '#92400e'],
    'exporting' => ['label' => 'Xuất kho',    'bg' => '#ede9fe', 'color' => '#5b21b6'],
    'shipping'  => ['label' => 'Đang giao',   'bg' => '#dbeafe', 'color' => '#1d4ed8'],
    'completed' => ['label' => 'Hoàn thành',  'bg' => '#d1fae5', 'color' => '#065f46'],
    'cancelled' => ['label' => 'Đã huỷ',      'bg' => '#fee2e2', 'color' => '#991b1b'],
  ];
  $sc = $statusConfig[$trip->status] ?? ['label' => $trip->status, 'bg' => '#f1f5f9', 'color' => '#64748b'];
@endphp

<div class="info-grid">
  <div class="info-row">
    <div class="info-label">Mã chuyến</div>
    <div class="info-value" style="font-family: Courier New, monospace; color: #0d9488; font-size: 11pt;">
      {{ $trip->trip_code }}
    </div>
  </div>
  <div class="info-row">
    <div class="info-label">Trạng thái</div>
    <div class="info-value">
      <span class="status-badge" style="background: {{ $sc['bg'] }}; color: {{ $sc['color'] }};">
        {{ $sc['label'] }}
      </span>
    </div>
  </div>
  <div class="info-row">
    <div class="info-label">Tài xế phụ trách</div>
    <div class="info-value">{{ $trip->driver?->name ?? '—' }}</div>
  </div>
  <div class="info-row">
    <div class="info-label">Kho xuất phát</div>
    <div class="info-value">{{ $trip->warehouse?->name ?? '—' }}</div>
  </div>
  <div class="info-row">
    <div class="info-label">Phương tiện</div>
    <div class="info-value">{{ $trip->vehicle_info }}</div>
  </div>
  <div class="info-row">
    <div class="info-label">Người tạo</div>
    <div class="info-value">{{ $trip->creator?->name ?? '—' }}</div>
  </div>
  @if($trip->notes)
  <div class="info-row">
    <div class="info-label">Ghi chú</div>
    <div class="info-value" style="color: #64748b; font-weight: 400;">{{ $trip->notes }}</div>
  </div>
  @endif
</div>

{{-- Timeline --}}
<div class="timeline-row">
  <div class="timeline-cell">
    <div class="timeline-label">📋 Ngày tạo</div>
    <div class="timeline-value">{{ $trip->created_at?->format('H:i – d/m/Y') ?? '—' }}</div>
  </div>
  <div class="timeline-cell">
    <div class="timeline-label">📤 Xuất kho</div>
    <div class="timeline-value">{{ $trip->exported_at?->format('H:i – d/m/Y') ?? '—' }}</div>
  </div>
  <div class="timeline-cell">
    <div class="timeline-label">🚛 Bắt đầu giao</div>
    <div class="timeline-value">{{ $trip->started_at?->format('H:i – d/m/Y') ?? '—' }}</div>
  </div>
  <div class="timeline-cell">
    <div class="timeline-label">✅ Hoàn thành</div>
    <div class="timeline-value">{{ $trip->completed_at?->format('H:i – d/m/Y') ?? '—' }}</div>
  </div>
</div>

{{-- ═══════════════ DANH SÁCH HÀNG HOÁ ═══════════════ --}}
<div class="section-title">📦 Danh sách hàng hoá</div>

<table>
  <thead>
    <tr>
      <th style="width:5%">STT</th>
      <th style="width:40%">Tên hàng hoá</th>
      <th style="width:20%">Danh mục</th>
      <th class="text-center" style="width:15%">Số lượng xuất</th>
      <th class="text-center" style="width:10%">Đã giao</th>
      <th class="text-center" style="width:10%">Đơn vị</th>
    </tr>
  </thead>
  <tbody>
    @forelse($trip->tripDetails as $i => $detail)
    <tr>
      <td class="text-center">{{ $i + 1 }}</td>
      <td><strong>{{ $detail->supply?->name ?? '—' }}</strong></td>
      <td>{{ $detail->supply?->category?->name ?? '—' }}</td>
      <td class="text-center" style="font-weight: bold; color: #7c3aed;">{{ number_format($detail->quantity_loaded) }}</td>
      <td class="text-center" style="font-weight: bold; color: #059669;">{{ number_format($detail->quantity_delivered) }}</td>
      <td class="text-center" style="color: #64748b;">{{ $detail->supply?->unit ?? '—' }}</td>
    </tr>
    @empty
    <tr>
      <td colspan="6" class="text-center" style="color: #94a3b8; padding: 12px;">Không có hàng hoá</td>
    </tr>
    @endforelse
  </tbody>
</table>

{{-- ═══════════════ DANH SÁCH ĐIỂM GIAO ═══════════════ --}}
<div class="section-title">📍 Danh sách điểm giao hàng</div>

@php
  $totalDeliveries = $trip->deliveries->count();
  $doneDeliveries  = $trip->deliveries->whereIn('status', ['success', 'warning'])->count();
@endphp

<table>
  <thead>
    <tr>
      <th style="width:4%">STT</th>
      <th style="width:20%">Họ và tên</th>
      <th style="width:18%">CCCD</th>
      <th style="width:12%">Số điện thoại</th>
      <th style="width:26%">Địa chỉ</th>
      <th class="text-center" style="width:10%">Trạng thái</th>
      <th class="text-center" style="width:10%">Thời gian giao</th>
    </tr>
  </thead>
  <tbody>
    @forelse($trip->deliveries as $i => $delivery)
    @php
      $hh = $delivery->household;
      $statusClass = match($delivery->status) {
        'success' => 'status-success',
        'pending' => 'status-pending',
        'failed'  => 'status-failed',
        'warning' => 'status-warning',
        default   => '',
      };
      $statusLabel = match($delivery->status) {
        'success' => '✓ Đã giao',
        'pending' => '⏳ Chờ',
        'failed'  => '✗ Thất bại',
        'warning' => '⚠ Cần xem xét',
        default   => $delivery->status,
      };
    @endphp
    <tr>
      <td class="text-center">{{ $i + 1 }}</td>
      <td><strong>{{ $hh?->household_name ?? $delivery->recipient_name }}</strong></td>
      <td style="font-family: Courier New, monospace; font-size: 7.5pt;">{{ $delivery->recipient_cccd ?? '—' }}</td>
      <td>{{ $hh?->phone ?? '—' }}</td>
      <td style="font-size: 7.5pt; color: #475569;">{{ $hh?->address ?? '—' }}</td>
      <td class="text-center {{ $statusClass }}">{{ $statusLabel }}</td>
      <td class="text-center" style="font-size: 7.5pt; color: #475569;">
        {{ $delivery->delivered_at?->format('H:i d/m/Y') ?? '—' }}
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="7" class="text-center" style="color: #94a3b8; padding: 12px;">Chưa có điểm giao nào</td>
    </tr>
    @endforelse
  </tbody>
</table>

{{-- Tổng kết --}}
<div style="background: #f0fdf4; border: 1px solid #86efac; border-radius: 6px; padding: 8px 12px; margin-top: 6px; font-size: 8.5pt;">
  <strong style="color: #059669;">Tổng kết:</strong>
  Đã giao <strong style="color: #059669;">{{ $doneDeliveries }}</strong> / {{ $totalDeliveries }} điểm
  @if($totalDeliveries > 0)
    — Tỷ lệ: <strong style="color: #059669;">{{ round($doneDeliveries / $totalDeliveries * 100) }}%</strong>
  @endif
</div>

{{-- ═══════════════ FOOTER ═══════════════ --}}
<div class="footer">
  <div class="footer-row">
    <div class="footer-left">
      <div style="font-weight: bold; color: #0f172a; margin-bottom: 4px;">HỆ THỐNG ĐẠI PHÚC</div>
      <div>Báo cáo được xuất tự động lúc: <strong>{{ now()->format('H:i – d/m/Y') }}</strong></div>
      <div style="margin-top: 4px; color: #94a3b8; font-size: 7.5pt;">
        Đây là tài liệu điện tử, có giá trị pháp lý tương đương bản giấy.
      </div>
    </div>
    <div class="footer-right">
      <div class="sign-box">
        <div class="sign-title">Đại diện ĐẠI PHÚC</div>
        <div class="sign-name">( Ký tên và đóng dấu )</div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
