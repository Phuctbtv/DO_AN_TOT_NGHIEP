@extends('layouts.app')
@section('title', 'Tạo Chuyến xe - ĐẠI PHÚC')

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: true }">

  @include('partials.admin-sidebar', ['activeMenu' => 'trips'])

  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => '➕ Tạo chuyến xe mới'])

    <div style="padding:1.5rem">

      <div style="font-size:.83rem;color:#9ca3af;margin-bottom:1.25rem">
        <a href="{{ route('admin.trips.index') }}" style="color:#0d9488;text-decoration:none">Chuyến xe</a>
        &rsaquo; Tạo mới
      </div>

      @if(session('error'))
        <div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:.875rem 1.25rem;border-radius:10px;margin-bottom:1.25rem">
          ❌ {{ session('error') }}
        </div>
      @endif

      @if($errors->any())
        <div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:.875rem 1.25rem;border-radius:10px;margin-bottom:1.25rem">
          <strong>⚠️ Vui lòng kiểm tra lại:</strong>
          <ul style="margin:.5rem 0 0 1rem;padding:0">
            @foreach($errors->all() as $err)
              <li style="font-size:.875rem">{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form id="tripForm" method="POST" action="{{ route('admin.trips.store') }}">
        @csrf

        <div style="display:grid;grid-template-columns:1fr 360px;gap:1.5rem;align-items:start">

          {{-- ===== CỘT TRÁI ===== --}}
          <div>

            {{-- KHO XUẤT --}}
            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:1.5rem;margin-bottom:1.25rem">
              <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin:0 0 1.25rem">🏭 Kho xuất hàng</h3>
              <div>
                <label style="display:block;font-size:.875rem;font-weight:600;color:#374151;margin-bottom:.5rem">
                  Chọn kho <span style="color:#ef4444">*</span>
                </label>
                <select name="warehouse_id" id="warehouseSelect" required
                        style="width:100%;padding:.65rem .875rem;border:1px solid {{ $errors->has('warehouse_id') ? '#ef4444' : '#e2e8f0' }};border-radius:8px;font-size:.875rem;background:#fff"
                        onchange="onWarehouseChange(this.value)">
                  <option value="">— Chọn kho —</option>
                  @foreach($warehouses as $wh)
                    <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>
                      {{ $wh->name }} &ndash; {{ Str::limit($wh->address, 50) }}
                    </option>
                  @endforeach
                </select>
                @error('warehouse_id')
                  <p style="color:#ef4444;font-size:.78rem;margin-top:.3rem">{{ $message }}</p>
                @enderror
              </div>
            </div>

            {{-- TÀI XẾ & PHƯƠNG TIỆN --}}
            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:1.5rem;margin-bottom:1.25rem">
              <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin:0 0 1.25rem">👨‍✈️ Tài xế & Phương tiện</h3>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
                <div>
                  <label style="display:block;font-size:.875rem;font-weight:600;color:#374151;margin-bottom:.5rem">
                    Tài xế <span style="color:#ef4444">*</span>
                  </label>
                  <select name="driver_id" required
                          style="width:100%;padding:.65rem .875rem;border:1px solid {{ $errors->has('driver_id') ? '#ef4444' : '#e2e8f0' }};border-radius:8px;font-size:.875rem;background:#fff">
                    <option value="">— Chọn tài xế —</option>
                    @foreach($drivers as $d)
                      <option value="{{ $d->id }}" {{ old('driver_id') == $d->id ? 'selected' : '' }}>
                        {{ $d->name }} ({{ $d->phone ?? $d->email }})
                      </option>
                    @endforeach
                  </select>
                  @if($drivers->isEmpty())
                    <p style="color:#f59e0b;font-size:.78rem;margin-top:.3rem">
                      ⚠️ Chưa có tài xế.
                      <a href="{{ route('admin.users.index') }}" style="color:#0d9488">Tạo tài khoản driver →</a>
                    </p>
                  @endif
                  @error('driver_id')
                    <p style="color:#ef4444;font-size:.78rem;margin-top:.3rem">{{ $message }}</p>
                  @enderror
                </div>
                <div>
                  <label style="display:block;font-size:.875rem;font-weight:600;color:#374151;margin-bottom:.5rem">
                    Phương tiện <span style="color:#ef4444">*</span>
                  </label>
                  <input type="text" name="vehicle_info" value="{{ old('vehicle_info') }}" required
                         placeholder="VD: Xe tải 51C-12345, 3.5 tấn"
                         style="width:100%;padding:.65rem .875rem;border:1px solid {{ $errors->has('vehicle_info') ? '#ef4444' : '#e2e8f0' }};border-radius:8px;font-size:.875rem;box-sizing:border-box">
                  @error('vehicle_info')
                    <p style="color:#ef4444;font-size:.78rem;margin-top:.3rem">{{ $message }}</p>
                  @enderror
                </div>
              </div>
              <div>
                <label style="display:block;font-size:.875rem;font-weight:600;color:#374151;margin-bottom:.5rem">Ghi chú</label>
                <textarea name="notes" rows="2" placeholder="Ghi chú thêm (tuỳ chọn)..."
                          style="width:100%;padding:.65rem .875rem;border:1px solid #e2e8f0;border-radius:8px;font-size:.875rem;resize:vertical;box-sizing:border-box">{{ old('notes') }}</textarea>
              </div>
            </div>

            {{-- DANH SÁCH HÀNG HOÁ --}}
            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:1.5rem">
              <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
                <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin:0">📦 Hàng hoá xuất kho</h3>
                <button type="button" id="addItemBtn" onclick="addItemRow()"
                        style="background:#f0fdf4;color:#10b981;border:1px solid #86efac;padding:.4rem .9rem;border-radius:8px;font-size:.8rem;font-weight:600;cursor:pointer">
                  ➕ Thêm mặt hàng
                </button>
              </div>

              {{-- Thông báo --}}
              <div id="msgSelectWarehouse" style="text-align:center;padding:1.5rem;color:#94a3b8;font-size:.875rem">
                👆 Chọn kho xuất để xem danh sách hàng có sẵn
              </div>
              <div id="msgLoading" style="display:none;text-align:center;padding:1rem;color:#64748b;font-size:.875rem">
                ⏳ Đang tải tồn kho...
              </div>
              <div id="msgNoStock" style="display:none;text-align:center;padding:1.25rem;color:#991b1b;background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;font-weight:600">
                ❌ Kho này chưa có hàng nhập kho — không thể tạo chuyến xe.<br>
                <span style="font-size:.82rem;font-weight:400;color:#7f1d1d">Vui lòng nhập hàng vào kho trước khi tạo chuyến xe.</span>
              </div>

              {{-- Bảng items --}}
              <div id="itemsWrap" style="display:none">
                @error('items')
                  <p style="color:#ef4444;font-size:.8rem;margin-bottom:.5rem">{{ $message }}</p>
                @enderror
                <table style="width:100%;border-collapse:collapse" id="itemsTable">
                  <thead>
                    <tr style="background:#f8fafc">
                      <th style="padding:.6rem .875rem;text-align:left;font-size:.75rem;font-weight:600;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0;width:45%">Mặt hàng</th>
                      <th style="padding:.6rem .875rem;text-align:center;font-size:.75rem;font-weight:600;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">Tồn kho</th>
                      <th style="padding:.6rem .875rem;text-align:center;font-size:.75rem;font-weight:600;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">Số lượng xuất</th>
                      <th style="padding:.6rem .875rem;border-bottom:1px solid #e2e8f0;width:40px"></th>
                    </tr>
                  </thead>
                  <tbody id="itemsBody"></tbody>
                </table>
                <p id="msgNoRows" style="text-align:center;padding:.75rem;color:#94a3b8;font-size:.85rem;margin:0">
                  Nhấn <strong>➕ Thêm mặt hàng</strong> để thêm hàng cần xuất kho
                </p>
              </div>

            </div>
          </div>

          {{-- ===== ĐIỂM GIAO HÀNG (full width, bên dưới 2 cột) ===== --}}
        </div>{{-- end grid 2 cột --}}

        {{-- Section hộ dân chọn điểm giao --}}
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:1.5rem;margin-bottom:1.5rem">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem">
            <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin:0">📍 Chọn điểm giao hàng (Hộ dân)</h3>
            <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap">
              {{-- Filter ưu tiên --}}
              <select id="priorityFilter" onchange="filterHouseholds(this.value)"
                      style="padding:.4rem .75rem;border:1px solid #e2e8f0;border-radius:8px;font-size:.82rem;background:#f8fafc">
                <option value="">Tất cả mức ưu tiên</option>
                <option value="1">🔴 Cấp 1 – Khẩn cấp</option>
                <option value="2">🟡 Cấp 2 – Quan trọng</option>
                <option value="3">🟢 Cấp 3 – Bình thường</option>
              </select>
              <button type="button" onclick="selectAllHouseholds(true)"
                      style="padding:.4rem .875rem;background:#dbeafe;color:#1d4ed8;border:none;border-radius:8px;font-size:.82rem;font-weight:600;cursor:pointer">
                ✅ Chọn tất cả
              </button>
              <button type="button" onclick="selectAllHouseholds(false)"
                      style="padding:.4rem .875rem;background:#f1f5f9;color:#64748b;border:none;border-radius:8px;font-size:.82rem;font-weight:600;cursor:pointer">
                ✕ Bỏ chọn
              </button>
              <span id="selectedCount" style="font-size:.82rem;font-weight:700;color:#0d9488;min-width:80px;text-align:right">
                Đã chọn: 0
              </span>
            </div>
          </div>

          @error('household_ids')
            <div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:.625rem 1rem;border-radius:8px;margin-bottom:.75rem;font-size:.875rem">
              ❌ {{ $message }}
            </div>
          @enderror

          @if($households->isEmpty())
            <div style="text-align:center;padding:2rem;color:#94a3b8;background:#f8fafc;border-radius:10px">
              <div style="font-size:2rem;margin-bottom:.5rem">🏠</div>
              Chưa có hộ dân nào được duyệt. Hãy vào
              <a href="{{ route('admin.households.pending') }}" style="color:#0d9488">Chờ duyệt</a>
              để phê duyệt hộ dân trước.
            </div>
          @else
            <div style="max-height:420px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:10px">
              <table style="width:100%;border-collapse:collapse" id="householdTable">
                <thead style="position:sticky;top:0;z-index:1">
                  <tr style="background:#f8fafc">
                    <th style="padding:.6rem .875rem;width:44px;border-bottom:1px solid #e2e8f0">
                      <input type="checkbox" id="chkAll" onchange="selectAllHouseholds(this.checked)"
                             style="width:16px;height:16px;cursor:pointer">
                    </th>
                    <th style="padding:.6rem .875rem;text-align:left;font-size:.75rem;font-weight:600;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">Họ tên</th>
                    <th style="padding:.6rem .875rem;text-align:left;font-size:.75rem;font-weight:600;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">CCCD</th>
                    <th style="padding:.6rem .875rem;text-align:left;font-size:.75rem;font-weight:600;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">SĐT</th>
                    <th style="padding:.6rem .875rem;text-align:left;font-size:.75rem;font-weight:600;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">Địa chỉ</th>
                    <th style="padding:.6rem .875rem;text-align:center;font-size:.75rem;font-weight:600;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">Ưu tiên</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($households as $hh)
                    @php
                      $priorityColors = [1 => ['#fee2e2','#ef4444','🔴'], 2 => ['#fef3c7','#f59e0b','🟡'], 3 => ['#d1fae5','#10b981','🟢']];
                      $pc = $priorityColors[$hh->priority_level] ?? ['#f1f5f9','#64748b','⬜'];
                      $oldIds = old('household_ids', []);
                    @endphp
                    <tr class="hh-row" data-priority="{{ $hh->priority_level }}"
                        style="border-bottom:1px solid #f1f5f9;cursor:pointer"
                        onclick="toggleRow(this)"
                        onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                      <td style="padding:.65rem .875rem;text-align:center" onclick="event.stopPropagation()">
                        <input type="checkbox" name="household_ids[]" value="{{ $hh->id }}"
                               class="hh-checkbox" onchange="updateCount()"
                               {{ in_array($hh->id, $oldIds) ? 'checked' : '' }}
                               style="width:16px;height:16px;cursor:pointer">
                      </td>
                      <td style="padding:.65rem .875rem;font-weight:600;color:#0f172a;font-size:.875rem">
                        {{ $hh->household_name }}
                      </td>
                      <td style="padding:.65rem .875rem;color:#64748b;font-size:.82rem;font-family:monospace">
                        {{ $hh->resident?->identity_card ?? '—' }}
                      </td>
                      <td style="padding:.65rem .875rem;color:#64748b;font-size:.82rem">
                        {{ $hh->phone ?? $hh->resident?->phone ?? '—' }}
                      </td>
                      <td style="padding:.65rem .875rem;color:#475569;font-size:.82rem;max-width:220px">
                        {{ Str::limit($hh->address, 60) }}
                      </td>
                      <td style="padding:.65rem .875rem;text-align:center">
                        <span style="background:{{ $pc[0] }};color:{{ $pc[1] }};padding:.2rem .65rem;border-radius:999px;font-size:.75rem;font-weight:700;white-space:nowrap">
                          {{ $pc[2] }} Cấp {{ $hh->priority_level }}
                        </span>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <p style="font-size:.78rem;color:#94a3b8;margin:.5rem 0 0">
              Tổng: {{ $households->count() }} hộ dân đã được duyệt. Một hộ dân có thể được giao nhiều chuyến xe.
            </p>
          @endif
        </div>

        {{-- Submit row full width --}}
        <div style="display:grid;grid-template-columns:1fr 360px;gap:1.5rem">
          <div></div>
          <div>

            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:1.5rem;margin-bottom:1rem">
              <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin:0 0 1rem">📋 Tóm tắt</h3>
              <div id="summaryBox" style="font-size:.875rem;color:#64748b">Điền thông tin để xem tóm tắt</div>
            </div>

            <div style="background:linear-gradient(135deg,#0d9488,#0891b2);border-radius:16px;padding:1.5rem">
              <button type="submit"
                      style="width:100%;background:#fff;color:#0d9488;border:none;padding:1rem;border-radius:10px;font-size:1rem;font-weight:800;cursor:pointer;margin-bottom:.75rem">
                🚛 TẠO CHUYẾN XE
              </button>
              <a href="{{ route('admin.trips.index') }}"
                 style="display:block;text-align:center;color:rgba(255,255,255,.75);font-size:.875rem;text-decoration:none">
                Huỷ, quay lại
              </a>
            </div>
          </div>{{-- end right col --}}
        </div>{{-- end submit grid --}}

      </form>
    </div>
  </main>
</div>
@endsection

@push('scripts')
<script>
// ===========================================================
//  DỮ LIỆU GỐC
// ===========================================================
// Tất cả supplies từ DB (fallback khi kho chưa có stock)
const ALL_SUPPLIES = {!! json_encode($suppliesJson) !!};

// Stock của kho đang chọn: {supplyId: qty}
let warehouseStock = {};
let allSuppliesMode = false;  // true = kho chưa có stock, dùng all supplies
let itemIndex = 0;

// ===========================================================
//  CHỌN KHO → AJAX load tồn kho
// ===========================================================
async function onWarehouseChange(warehouseId) {
  // Reset
  warehouseStock  = {};
  allSuppliesMode = false;
  document.getElementById('itemsBody').innerHTML = '';
  itemIndex = 0;

  if (!warehouseId) {
    show('msgSelectWarehouse');
    hide('msgLoading'); hide('msgNoStock'); hide('itemsWrap');
    updateSummary(); return;
  }

  show('msgLoading');
  hide('msgSelectWarehouse'); hide('msgNoStock'); hide('itemsWrap');

  try {
    const res  = await fetch('/admin/trips/stock/' + warehouseId);
    const data = await res.json(); // [{id, name, unit, category, stock}, ...]

    if (data.length > 0) {
      data.forEach(function(s) { warehouseStock[s.id] = { stock: s.stock, unit: s.unit, name: s.name, category: s.category }; });
      allSuppliesMode = false;
      document.getElementById('addItemBtn').style.display = 'inline-flex';
      show('itemsWrap');
    } else {
      // Kho chưa nhập hàng → CHẶN, không cho tạo chuyến
      allSuppliesMode = true;
      document.getElementById('addItemBtn').style.display = 'none';
      hide('itemsWrap');
      show('msgNoStock');
    }
  } catch(e) {
    allSuppliesMode = true;
    document.getElementById('addItemBtn').style.display = 'none';
    hide('itemsWrap');
    show('msgNoStock');
  }

  hide('msgLoading');
  show('itemsWrap');
  document.getElementById('msgNoRows').style.display = 'block';
  updateSummary();
}

// ===========================================================
//  THÊM DÒNG
// ===========================================================
function addItemRow() {
  const warehouseId = document.getElementById('warehouseSelect').value;
  if (!warehouseId) {
    alert('Vui lòng chọn kho xuất trước!');
    return;
  }
  if (allSuppliesMode) return; // Kho không có hàng — chặn
  show('itemsWrap');
  hide('msgSelectWarehouse');
  document.getElementById('msgNoRows').style.display = 'none';

  const idx = itemIndex++;

  // Build options
  let optionsHtml = '<option value="">— Chọn mặt hàng —</option>';

  if (allSuppliesMode) {
    // Dùng tất cả supplies (kho chưa có stock)
    ALL_SUPPLIES.forEach(function(s) {
      optionsHtml += '<option value="' + s.id + '" data-stock="0" data-unit="' + escHtml(s.unit) + '">'
        + escHtml(s.name) + ' (' + escHtml(s.category) + ') – ' + escHtml(s.unit) + '</option>';
    });
  } else {
    // Chỉ dùng supplies có hàng trong kho
    Object.keys(warehouseStock).forEach(function(id) {
      const s = warehouseStock[id];
      optionsHtml += '<option value="' + id + '" data-stock="' + s.stock + '" data-unit="' + escHtml(s.unit) + '">'
        + escHtml(s.name) + ' (' + escHtml(s.category) + ') – còn ' + s.stock + ' ' + escHtml(s.unit) + '</option>';
    });
  }

  const row = document.createElement('tr');
  row.id = 'item-row-' + idx;
  row.style.borderBottom = '1px solid #f1f5f9';
  row.innerHTML =
    '<td style="padding:.5rem .875rem">' +
      '<select name="items[' + idx + '][supply_id]" required onchange="onSelectChange(this,' + idx + ')"' +
             ' style="width:100%;padding:.5rem;border:1px solid #e2e8f0;border-radius:6px;font-size:.82rem">' +
        optionsHtml +
      '</select>' +
    '</td>' +
    '<td id="stock-' + idx + '" style="padding:.5rem .875rem;text-align:center;font-size:.82rem;white-space:nowrap">' +
      '<span style="color:#94a3b8">—</span>' +
    '</td>' +
    '<td style="padding:.5rem .875rem;text-align:center">' +
      '<input type="number" name="items[' + idx + '][quantity_loaded]" id="qty-' + idx + '"' +
             ' min="1" value="1" required' +
             ' oninput="updateSummary()"' +
             ' style="width:80px;padding:.4rem;border:1px solid #e2e8f0;border-radius:6px;font-size:.875rem;text-align:center">' +
    '</td>' +
    '<td style="padding:.5rem .875rem;text-align:center">' +
      '<button type="button" onclick="removeRow(' + idx + ')"' +
              ' style="background:#fee2e2;color:#ef4444;border:none;width:28px;height:28px;border-radius:6px;cursor:pointer;font-size:.85rem">✕</button>' +
    '</td>';

  document.getElementById('itemsBody').appendChild(row);
  updateSummary();
}

function onSelectChange(sel, idx) {
  const opt   = sel.options[sel.selectedIndex];
  const stock = opt ? parseInt(opt.getAttribute('data-stock') || '0') : 0;
  const unit  = opt ? opt.getAttribute('data-unit') || '' : '';

  const cell  = document.getElementById('stock-' + idx);
  const qtyEl = document.getElementById('qty-' + idx);

  if (opt && opt.value) {
    if (stock <= 0) {
      cell.innerHTML = '<span style="color:#ef4444;font-weight:600">Hết hàng (0)</span>';
    } else {
      cell.innerHTML = '<span style="font-weight:700;color:#0d9488">' + stock + '</span>'
                     + ' <span style="color:#94a3b8;font-size:.75rem">' + unit + '</span>';
      if (qtyEl) qtyEl.max = stock;
    }
  } else {
    cell.innerHTML = '<span style="color:#94a3b8">—</span>';
  }
  updateSummary();
}

function removeRow(idx) {
  const row = document.getElementById('item-row-' + idx);
  if (row) row.remove();
  if (!document.getElementById('itemsBody').children.length) {
    document.getElementById('msgNoRows').style.display = 'block';
  }
  updateSummary();
}

// ===========================================================
//  TÓM TẮT
// ===========================================================
function updateSummary() {
  const whSel  = document.getElementById('warehouseSelect');
  const whName = whSel.options[whSel.selectedIndex] ? whSel.options[whSel.selectedIndex].text : '—';
  const rows   = document.querySelectorAll('#itemsBody tr');

  let html = '<div style="margin-bottom:.75rem">'
           + '<div style="font-size:.7rem;color:#94a3b8;text-transform:uppercase;font-weight:600;margin-bottom:.2rem">Kho xuất</div>'
           + '<div style="font-weight:600;color:#0f172a">' + (whName === '— Chọn kho —' ? '<span style="color:#ef4444">Chưa chọn</span>' : whName) + '</div>'
           + '</div>';

  if (rows.length) {
    html += '<div style="font-size:.7rem;color:#94a3b8;text-transform:uppercase;font-weight:600;margin-bottom:.4rem">Hàng hoá (' + rows.length + ' loại)</div>';
    rows.forEach(function(row) {
      const sel = row.querySelector('select');
      const qty = row.querySelector('input[type=number]');
      if (sel && sel.value && qty) {
        const name = sel.options[sel.selectedIndex].text.split('(')[0].trim();
        html += '<div style="display:flex;justify-content:space-between;padding:.3rem 0;border-bottom:1px solid #f1f5f9;font-size:.82rem">'
              + '<span style="color:#475569">' + name + '</span>'
              + '<span style="font-weight:700;color:#0d9488">' + qty.value + '</span>'
              + '</div>';
      }
    });
  } else {
    html += '<div style="color:#94a3b8;font-size:.82rem;text-align:center;padding:.5rem">Chưa có mặt hàng</div>';
  }

  // Số hộ dân đã chọn
  const hhCount = document.querySelectorAll('.hh-checkbox:checked').length;
  html += '<div style="margin-top:.75rem;padding:.5rem .75rem;background:' + (hhCount > 0 ? '#d1fae5' : '#fee2e2') + ';border-radius:8px;font-size:.82rem;font-weight:600;color:' + (hhCount > 0 ? '#065f46' : '#991b1b') + '">'
        + '📍 Điểm giao: ' + hhCount + ' hộ dân'
        + (hhCount === 0 ? ' <span style="font-weight:400">(bắt buộc)</span>' : '')
        + '</div>';

  document.getElementById('summaryBox').innerHTML = html;
}

// ===========================================================
//  HELPERS
// ===========================================================
function show(id) { document.getElementById(id).style.display = 'block'; }
function hide(id) { document.getElementById(id).style.display = 'none'; }
function escHtml(str) {
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ===========================================================
//  HOUSEHOLD SELECTION
// ===========================================================

function filterHouseholds(priority) {
  document.querySelectorAll('.hh-row').forEach(function(row) {
    if (!priority || row.dataset.priority === priority) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}

function selectAllHouseholds(checked) {
  const filter = document.getElementById('priorityFilter').value;
  document.querySelectorAll('.hh-row').forEach(function(row) {
    // Chỉ chọn những hàng đang hiển thị (theo filter)
    if (!filter || row.dataset.priority === filter) {
      const chk = row.querySelector('.hh-checkbox');
      if (chk) chk.checked = checked;
    }
  });
  document.getElementById('chkAll').checked = checked;
  updateCount();
}

function toggleRow(row) {
  const chk = row.querySelector('.hh-checkbox');
  if (chk) {
    chk.checked = !chk.checked;
    updateCount();
  }
}

function updateCount() {
  const total = document.querySelectorAll('.hh-checkbox:checked').length;
  document.getElementById('selectedCount').textContent = 'Đã chọn: ' + total;

  // Cập nhật chkAll state
  const all = document.querySelectorAll('.hh-checkbox').length;
  document.getElementById('chkAll').indeterminate = total > 0 && total < all;
  document.getElementById('chkAll').checked = total > 0 && total === all;

  updateSummary();
}

// Khởi tạo nếu có old value sau validation fail
window.addEventListener('DOMContentLoaded', function() {
  const wh = document.getElementById('warehouseSelect').value;
  if (wh) onWarehouseChange(wh);
  updateCount(); // đếm household đã check (old value)
  updateSummary();
});
</script>
@endpush
